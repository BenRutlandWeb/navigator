<?php

namespace Navigator\Mail\Concerns;

use Navigator\Mail\Mailable;
use Navigator\Mail\Mailer;

trait Notifiable
{
    public function notify(Mailable $mail): bool
    {
        return Mailer::make()->to($this)->send($mail);
    }
}
