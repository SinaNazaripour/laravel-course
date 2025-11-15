<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;

class NotificationDispatcher
{
    private array $notification_channels;

    public function __construct(NotificationServiceInterface ...$notification_channels)
    {
        $this->notification_channels = $notification_channels;
    }

    public function sendNotification($message)
    {
        foreach ($this->notification_channels as $notifier) {
            echo $notifier->sendNotification($message);
        }
    }
}
