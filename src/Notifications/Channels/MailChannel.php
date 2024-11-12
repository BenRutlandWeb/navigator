<?php

namespace Navigator\Notifications\Channels;

use Navigator\Mail\Mailable;
use Navigator\Mail\MailFactory;
use Navigator\Notifications\ChannelInterface;
use Navigator\Notifications\NotificationInterface;
use RuntimeException;

class MailChannel implements ChannelInterface
{
    public function __construct(protected MailFactory $mailer)
    {
        //
    }
    public function send(mixed $notifiable, NotificationInterface $notification): void
    {
        $this->mailer->to($notifiable)->send($this->getMail($notifiable, $notification));
    }

    public function getMail(mixed $notifiable, NotificationInterface $notification): Mailable
    {
        if (method_exists($notification, 'toMail')) {
            return call_user_func([$notification, 'toMail'], $notifiable);
        }

        throw new RuntimeException('Notification is missing toMail method.');
    }
}
