<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $codigo_transaccion
 * @property int $reserva_id
 * @property int $user_id
 * @property string $metodo_pago
 * @property numeric $monto
 * @property PaymentStatus $estado
 * @property string|null $referencia_externa
 * @property array<array-key, mixed>|null $datos_transaccion
 * @property string|null $motivo_rechazo
 * @property \Illuminate\Support\Carbon|null $fecha_aprobacion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \App\Models\Reservation $reservation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionLog> $transactionLogs
 * @property-read int|null $transaction_logs_count
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Payment aprobados()
 * @method static Builder<static>|Payment delUsuario(int $userId)
 * @method static Builder<static>|Payment enRango($desde, $hasta)
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment onlyTrashed()
 * @method static Builder<static>|Payment pendientes()
 * @method static Builder<static>|Payment porMetodo(string $metodo)
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment rechazados()
 * @method static Builder<static>|Payment reembolsados()
 * @method static Builder<static>|Payment whereCodigoTransaccion($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereDatosTransaccion($value)
 * @method static Builder<static>|Payment whereDeletedAt($value)
 * @method static Builder<static>|Payment whereEstado($value)
 * @method static Builder<static>|Payment whereFechaAprobacion($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereMetodoPago($value)
 * @method static Builder<static>|Payment whereMonto($value)
 * @method static Builder<static>|Payment whereMotivoRechazo($value)
 * @method static Builder<static>|Payment whereReferenciaExterna($value)
 * @method static Builder<static>|Payment whereReservaId($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @method static Builder<static>|Payment whereUserId($value)
 * @method static Builder<static>|Payment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Payment withoutTrashed()
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_transaccion',
        'reserva_id',
        'user_id',
        'metodo_pago',
        'monto',
        'estado',
        'referencia_externa',
        'datos_transaccion',
        'motivo_rechazo',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'estado' => PaymentStatus::class,
        'monto' => 'decimal:2',
        'fecha_aprobacion' => 'datetime',
        'datos_transaccion' => 'array',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'metodo_pago', 'tipo');
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'payment_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', PaymentStatus::Pendiente);
    }

    public function scopeAprobados(Builder $query): Builder
    {
        return $query->where('estado', PaymentStatus::Aprobado);
    }

    public function scopeRechazados(Builder $query): Builder
    {
        return $query->where('estado', PaymentStatus::Rechazado);
    }

    public function scopeReembolsados(Builder $query): Builder
    {
        return $query->where('estado', PaymentStatus::Reembolsado);
    }

    public function scopeDelUsuario(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorMetodo(Builder $query, string $metodo): Builder
    {
        return $query->where('metodo_pago', $metodo);
    }

    public function scopeEnRango(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Verificar si está pendiente
     */
    public function isPendiente(): bool
    {
        return $this->estado === PaymentStatus::Pendiente;
    }

    /**
     * Verificar si fue aprobado
     */
    public function isAprobado(): bool
    {
        return $this->estado === PaymentStatus::Aprobado;
    }

    /**
     * Verificar si fue rechazado
     */
    public function isRechazado(): bool
    {
        return $this->estado === PaymentStatus::Rechazado;
    }

    /**
     * Verificar si fue reembolsado
     */
    public function isReembolsado(): bool
    {
        return $this->estado === PaymentStatus::Reembolsado;
    }

    /**
     * Verificar si puede ser reembolsado
     */
    public function canBeRefunded(): bool
    {
        return $this->estado->canBeRefunded();
    }

    /**
     * Aprobar pago
     */
    public function approve(?string $referenciaExterna = null): bool
    {
        if (!$this->isPendiente()) {
            return false;
        }

        $this->estado = PaymentStatus::Aprobado;
        $this->fecha_aprobacion = now();

        if ($referenciaExterna) {
            $this->referencia_externa = $referenciaExterna;
        }

        $saved = $this->save();

        if ($saved) {
            $this->logTransaction('aprobacion', 'Pago aprobado exitosamente');

            // Actualizar estado de la reserva
            if ($this->reservation && $this->reservation->isPendiente()) {
                $this->reservation->update(['estado' => 'confirmada']);
            }
        }

        return $saved;
    }

    /**
     * Rechazar pago
     */
    public function reject(string $motivo): bool
    {
        if (!$this->isPendiente()) {
            return false;
        }

        $this->estado = PaymentStatus::Rechazado;
        $this->motivo_rechazo = $motivo;
        $saved = $this->save();

        if ($saved) {
            $this->logTransaction('rechazo', "Pago rechazado: {$motivo}");
        }

        return $saved;
    }

    /**
     * Reembolsar pago
     */
    public function refund(string $motivo): bool
    {
        if (!$this->canBeRefunded()) {
            return false;
        }

        $this->estado = PaymentStatus::Reembolsado;
        $this->motivo_rechazo = $motivo;
        $saved = $this->save();

        if ($saved) {
            $this->logTransaction('reembolso', "Pago reembolsado: {$motivo}");

            // Actualizar estado de la reserva a cancelada
            if ($this->reservation && !$this->reservation->isCancelada()) {
                $this->reservation->update([
                    'estado' => 'cancelada',
                    'motivo_cancelacion' => "Reembolso: {$motivo}"
                ]);
            }
        }

        return $saved;
    }

    /**
     * Registrar log de transacción
     */
    public function logTransaction(string $accion, string $descripcion, ?array $datos = null): void
    {
        TransactionLog::create([
            'payment_id' => $this->id,
            'user_id' => Auth::id(),
            'accion' => $accion,
            'descripcion' => $descripcion,
            'datos' => $datos,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Generar código único de transacción
     */
    public static function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "TXN-{$date}-{$random}";
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->codigo_transaccion)) {
                $payment->codigo_transaccion = self::generateTransactionCode();
            }
        });
    }
}
