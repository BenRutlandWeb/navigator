<?php

namespace Navigator\Contracts;

use Navigator\Events\Dispatcher;

interface SubscriberInterface
{
    public function subscribe(Dispatcher $dispatcher): void;
}
