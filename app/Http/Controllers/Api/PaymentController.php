<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentController extends BaseApiController
{
    protected \App\Services\MailNotificationService $mailService;
    /**
     * Crear intención de pago
     *
     */
    use AuthorizesRequests;

    public function createPaymentIntent(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);

        $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mercadopago',
        ]);

        // Verificar que la reserva no tenga pago aprobado
        if ($reservation->hasPaidPayment()) {
            return $this->error('Esta reserva ya tiene un pago aprobado');
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'reserva_id' => $reservation->id,
                'user_id' => $request->user()->id,
                'metodo_pago' => $request->metodo_pago,
                'monto' => $reservation->monto_final,
                'estado' => 'pendiente',
            ]);

            $payment->logTransaction('creacion', 'Pago creado en estado pendiente');

            DB::commit();

            return $this->created(
                new PaymentResource($payment),
                'Intención de pago creada. Proceda con el pago.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError('Error al crear intención de pago: ' . $e->getMessage());
        }
    }

    /**
     * Procesar pago (simulación - sin pasarela real)
     */
    public function __construct()
    {
        $this->mailService = app(\App\Services\MailNotificationService::class);
    }

    public function processPayment(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('update', $payment->reservation);

        if (!$payment->isPendiente()) {
            return $this->error('Este pago ya fue procesado');
        }

        $request->validate([
            'referencia_externa' => 'nullable|string',
            'datos_transaccion' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Simular procesamiento de pago
            $success = true; // En producción aquí iría la lógica de la pasarela

            if ($success) {
                $payment->approve($request->referencia_externa);
                
                if ($request->datos_transaccion) {
                    $payment->update(['datos_transaccion' => $request->datos_transaccion]);
                }

                DB::commit();
                $payment = $payment->fresh(['reservation', 'user']);
                $this->mailService->sendPaymentProcessed($payment);
                return $this->success(
                    new PaymentResource($payment),
                    'Pago procesado exitosamente'
                );
            } else {
                $payment->reject('Error en el procesamiento');
                DB::commit();

                return $this->error('El pago fue rechazado');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError('Error al procesar pago: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar pago manualmente (admin)
     */
    public function approve(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('manage-payments');

        $request->validate([
            'referencia_externa' => 'nullable|string',
        ]);

        if (!$payment->isPendiente()) {
            return $this->error('Este pago no está pendiente');
        }

        try {
            DB::beginTransaction();

            $payment->approve($request->referencia_externa);

            DB::commit();

            return $this->success(
                new PaymentResource($payment->fresh()),
                'Pago aprobado exitosamente'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError('Error al aprobar pago: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar pago (admin)
     */
    public function reject(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('manage-payments');

        $request->validate([
            'motivo' => 'required|string|min:10',
        ]);

        if (!$payment->isPendiente()) {
            return $this->error('Este pago no está pendiente');
        }

        try {
            DB::beginTransaction();

            $payment->reject($request->motivo);

            DB::commit();

            return $this->success(
                new PaymentResource($payment->fresh()),
                'Pago rechazado'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError('Error al rechazar pago: ' . $e->getMessage());
        }
    }

    /**
     * Reembolsar pago (admin)
     */
    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('manage-payments');

        $request->validate([
            'motivo' => 'required|string|min:10',
        ]);

        if (!$payment->canBeRefunded()) {
            return $this->error('Este pago no puede ser reembolsado');
        }

        try {
            DB::beginTransaction();

            $payment->refund($request->motivo);

            DB::commit();

            return $this->success(
                new PaymentResource($payment->fresh()),
                'Pago reembolsado exitosamente'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError('Error al reembolsar pago: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de pago
     */
    public function show(Payment $payment): JsonResponse
    {
        $user = request()->user();

        // Solo el usuario dueño o admin pueden ver el pago
        if ($payment->user_id !== $user->id && !$user->hasRole('admin')) {
            return $this->unauthorized();
        }

        $payment->load(['reservation', 'user', 'transactionLogs']);

        return $this->success(new PaymentResource($payment));
    }

    /**
     * Listar pagos del usuario autenticado
     */
    public function myPayments(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->input('status');

        $query = Payment::where('user_id', $user->id)
            ->with(['reservation']);

        if ($status) {
            $query->where('estado', $status);
        }

        $payments = $query->orderByDesc('created_at')->get();

        return $this->success(PaymentResource::collection($payments));
    }

    /**
     * Listar todos los pagos (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('manage-payments');

        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $metodo = $request->input('metodo_pago');

        $query = Payment::with(['user', 'reservation']);

        if ($status) {
            $query->where('estado', $status);
        }

        if ($metodo) {
            $query->where('metodo_pago', $metodo);
        }

        $payments = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->success([
            'payments' => PaymentResource::collection($payments),
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ],
        ]);
    }

    /**
     * Métodos de pago disponibles
     */
    public function paymentMethods(): JsonResponse
    {
        $methods = [
            ['value' => 'efectivo', 'label' => 'Efectivo', 'enabled' => true],
            ['value' => 'tarjeta', 'label' => 'Tarjeta de Crédito/Débito', 'enabled' => true],
            ['value' => 'transferencia', 'label' => 'Transferencia Bancaria', 'enabled' => true],
            ['value' => 'mercadopago', 'label' => 'Mercado Pago', 'enabled' => false], // Deshabilitado por ahora
        ];

        return $this->success($methods);
    }
}