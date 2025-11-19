<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'reservation-created');
        $preview = $request->boolean('preview');
        $mailService = app(\App\Services\MailNotificationService::class);

        switch ($type) {
            case 'reservation-created':
                $reservation = \App\Models\Reservation::orderByDesc('id')->firstOrFail();
                if ($preview) {
                    return view('emails.reservation-created', ['reservation' => $reservation]);
                }
                $mailService->sendReservationCreated($reservation);
                break;

            case 'reservation-cancelled':
                $reservation = \App\Models\Reservation::where('estado', \App\Enums\ReservationStatus::Cancelada)
                    ->orderByDesc('id')
                    ->first() ?? \App\Models\Reservation::orderByDesc('id')->firstOrFail();
                if ($preview) {
                    return view('emails.reservation-cancelled', ['reservation' => $reservation]);
                }
                $mailService->sendReservationCancelled($reservation);
                break;

            case 'payment-processed':
                $payment = \App\Models\Payment::where('estado', 'aprobado')
                    ->with(['reservation', 'user'])
                    ->orderByDesc('id')
                    ->first() ?? \App\Models\Payment::with(['reservation', 'user'])->orderByDesc('id')->firstOrFail();
                if ($preview) {
                    return view('emails.payment-processed', ['payment' => $payment]);
                }
                $mailService->sendPaymentProcessed($payment);
                break;

            case 'user-login':
                $user = \App\Models\User::orderByDesc('id')->firstOrFail();
                if ($preview) {
                    return view('emails.user-login', ['user' => $user]);
                }
                $mailService->sendUserLogin($user);
                break;
        }

        return response()->json(['success' => true, 'queued' => true, 'type' => $type]);
    }
}
