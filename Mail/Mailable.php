<?php

namespace Navigator\Mail;

use Navigator\Foundation\Concerns\Htmlable;
use Navigator\Http\Response;

abstract class Mailable
{
    public function envelope(Mailer $mailer): void
    {
        //
    }

    abstract public function subject(): string;

    abstract public function content(): Response|Htmlable|string;

    /** @return array<int, string> */
    public function attachments(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return $this->content();
    }
}
