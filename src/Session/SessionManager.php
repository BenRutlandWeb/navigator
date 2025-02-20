<?php

namespace Navigator\Session;

use SessionHandlerInterface;

class SessionManager
{
    public function __construct(protected SessionHandlerInterface $handler)
    {
        //
    }

    public function start(): void
    {
        if (!headers_sent() && session_status() !== PHP_SESSION_ACTIVE) {
            session_set_save_handler($this->handler, true);
            session_start(['use_strict_mode' => 1]);
        }
    }
}
