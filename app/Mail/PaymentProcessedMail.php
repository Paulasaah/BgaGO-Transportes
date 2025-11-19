<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentProcessedMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment->loadMissing(['reservation']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pago procesado');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-processed');
    }
}