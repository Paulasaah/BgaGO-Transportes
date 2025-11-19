<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\MailNotificationService;

class SendUserLoginEmail
{
    protected MailNotificationService $mailService;

    public function __construct(MailNotificationService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function handle(Login $event): void
    {
        $this->mailService->sendUserLogin($event->user);
    }
}