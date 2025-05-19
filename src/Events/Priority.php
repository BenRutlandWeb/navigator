<?php

namespace Navigator\Events;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_FUNCTION)]
final readonly class Priority
{
    const int LOW = 100;
    const int NORMAL = 10;
    const int HIGH = 1;

    public function __construct(public int $priority = self::NORMAL)
    {
        //
    }
}
