<?php

namespace Navigator\Queue\Concerns;

use Carbon\CarbonInterface;

trait Queueable
{
    public CarbonInterface|int $delay = 0;

    public function delay(CarbonInterface|int $delay = 0): static
    {
        $this->delay = $delay;

        return $this;
    }
}
