<?php

namespace Navigator\Mail;

use Navigator\Contracts\MailableInterface;

interface EnvelopeInterface
{
    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function to(iterable|MailableInterface|string $address, string $name = ''): EnvelopeInterface;

    public function from(MailableInterface|string $address, string $name = ''): EnvelopeInterface;

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function cc(iterable|MailableInterface|string $address, string $name = ''): EnvelopeInterface;

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function bcc(iterable|MailableInterface|string $address, string $name = ''): EnvelopeInterface;

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function replyTo(iterable|MailableInterface|string $address, string $name = ''): EnvelopeInterface;
}
