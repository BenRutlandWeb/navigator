<?php

namespace Navigator\Notifications\Concerns;

use Navigator\Notifications\Notification;
use Navigator\Notifications\NotificationInterface;

trait Notifiable
{
    public function notify(NotificationInterface $notification): void
    {
        Notification::send($this, $notification);
    }

    public function notifyNow(NotificationInterface $notification, ?array $channels = null): void
    {
        Notification::sendNow($this, $notification, $channels);
    }
}
