<?php

namespace Navigator\Notifications\Channels;

use Navigator\Mail\Mailable;
use Navigator\Mail\Mailer;
use Navigator\Notifications\ChannelInterface;
use Navigator\Notifications\NotificationInterface;
use RuntimeException;

/**
 * @todo
 * remove reliance on helper method. Mailer is currently a binding instead of a
 * singleton so it needs to act as a factory to return a new object each time.
 * Once that is in place, the factory can be passd in the constructor.
 */

use function Navigator\app;

class MailChannel implements ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification): void
    {
        app(Mailer::class)->to($notifiable)->send($this->getMail($notifiable, $notification));
    }

    public function getMail(mixed $notifiable, NotificationInterface $notification): Mailable
    {
        if (method_exists($notification, 'toMail')) {
            return call_user_func([$notification, 'toMail'], $notifiable);
        }

        throw new RuntimeException('Notification is missing toMail method.');
    }
}
