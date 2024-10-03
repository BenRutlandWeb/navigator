<?php

namespace Navigator\Notifications;

interface ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification): void;
}
