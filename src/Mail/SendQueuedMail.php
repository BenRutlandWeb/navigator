<?php

namespace Navigator\Mail;

use Navigator\Queue\Job;

class SendQueuedMail extends Job
{
    public function __construct(
        protected string|array $to,
        protected string $subject,
        protected string $content,
        protected array $headers = [],
        protected array $attachments = []
    ) {
        # do something
    }

    public function handle(): bool
    {
        return wp_mail(
            $this->to,
            $this->subject,
            $this->content,
            $this->headers,
            $this->attachments
        );
    }
}
