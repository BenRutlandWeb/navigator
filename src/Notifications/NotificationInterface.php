<?php

namespace Navigator\Notifications;

interface NotificationInterface
{
    public function via(mixed $notifiable): array;
}
