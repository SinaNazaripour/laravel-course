<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;

class EmailNotificationService implements NotificationServiceInterface
{
    public function sendNotification($message)
    {
        return "Email sent!" . $message;
    }
}
