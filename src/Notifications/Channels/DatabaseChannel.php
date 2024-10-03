<?php

namespace Navigator\Notifications\Channels;

use Navigator\Notifications\ChannelInterface;
use Navigator\Notifications\NotificationInterface;
use Navigator\Str\Str;
use RuntimeException;

class DatabaseChannel implements ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification): void
    {
        $notifiable->unreadNotifications()->create([
            'post_title'   => Str::uuid(),
            'post_content' => maybe_serialize($this->getData($notifiable, $notification)),
        ]);
    }

    public function getData(mixed $notifiable, NotificationInterface $notification): array
    {
        if (method_exists($notification, 'toDatabase')) {
            return call_user_func([$notification, 'toDatabase'], $notifiable);
        }

        throw new RuntimeException('Notification is missing toDatabase method.');
    }
}
