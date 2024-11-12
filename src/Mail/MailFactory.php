<?php

namespace Navigator\Mail;

use Navigator\Contracts\MailableInterface;
use Navigator\Events\Dispatcher;

class MailFactory implements EnvelopeInterface
{
    public function __construct(public readonly Dispatcher $dispatcher)
    {
        //
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function to(iterable|MailableInterface|string $address, string $name = ''): PendingMail
    {
        return (new PendingMail($this))->to($address, $name);
    }

    public function from(MailableInterface|string $address, string $name = ''): PendingMail
    {
        return (new PendingMail($this))->from($address, $name);
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function cc(iterable|MailableInterface|string $address, string $name = ''): PendingMail
    {
        return (new PendingMail($this))->cc($address, $name);
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function bcc(iterable|MailableInterface|string $address, string $name = ''): PendingMail
    {
        return (new PendingMail($this))->bcc($address, $name);
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function replyTo(iterable|MailableInterface|string $address, string $name = ''): PendingMail
    {
        return (new PendingMail($this))->replyTo($address, $name);
    }

    public function send(Mailable $mail): bool
    {
        return (new PendingMail($this))->send($mail);
    }
}
