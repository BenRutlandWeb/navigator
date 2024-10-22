<?php

namespace Navigator\Notifications\Channels;

use Navigator\Foundation\Application;
use Navigator\Mail\Mailable;
use Navigator\Mail\Mailer;
use Navigator\Notifications\ChannelInterface;
use Navigator\Notifications\NotificationInterface;
use RuntimeException;

class MailChannel implements ChannelInterface
{
    public function __construct(protected Application $app)
    {
        //
    }

    public function send(mixed $notifiable, NotificationInterface $notification): void
    {
        $this->app->get(Mailer::class)
            ->to($notifiable)
            ->send($this->getMail($notifiable, $notification));
    }

    public function getMail(mixed $notifiable, NotificationInterface $notification): Mailable
    {
        if (method_exists($notification, 'toMail')) {
            return call_user_func([$notification, 'toMail'], $notifiable);
        }

        throw new RuntimeException('Notification is missing toMail method.');
    }
}
