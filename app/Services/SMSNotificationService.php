<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;

class SMSNotificationService implements NotificationServiceInterface
{
    public function sendNotification($message)
    {
        return "SMS sent!" . $message;
    }
}
