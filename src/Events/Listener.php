<?php

namespace Navigator\Events;

use Navigator\Foundation\Application;

abstract class Listener
{
    public function __construct(protected Application $app)
    {
        //
    }
}
