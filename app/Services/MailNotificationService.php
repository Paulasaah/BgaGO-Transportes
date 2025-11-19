<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Payment;
use App\Mail\ReservationCreatedMail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentProcessedMail;
use App\Mail\UserLoginMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Auth\Authenticatable;

class MailNotificationService
{
    public function sendReservationCreated(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new ReservationCreatedMail($reservation));
        }
    }

    public function sendReservationCancelled(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new ReservationCancelledMail($reservation));
        }
    }

    public function sendPaymentProcessed(Payment $payment): void
    {
        $user = $payment->user;
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new PaymentProcessedMail($payment));
        }
    }

    public function sendUserLogin(Authenticatable $user): void
    {
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new UserLoginMail($user));
        }
    }
}