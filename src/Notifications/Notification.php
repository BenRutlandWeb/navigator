<?php

namespace Navigator\Notifications;

class Notification
{
    protected static $sender;

    public static function setNotificationSender(NotificationSender $sender)
    {
        static::$sender = $sender;
    }

    public static function send(mixed $notifiables, NotificationInterface $notification): void
    {
        static::$sender->send($notifiables, $notification);
    }

    public static function sendNow(mixed $notifiables, NotificationInterface $notification, ?array $channels = null): void
    {
        static::$sender->sendNow($notifiables, $notification, $channels);
    }
}
