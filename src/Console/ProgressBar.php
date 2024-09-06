<?php

namespace Navigator\Console;

use function WP_CLI\Utils\make_progress_bar as wpcli_make_progress_bar;

class ProgressBar
{
    protected string $message = '';

    protected $bar;

    public function __construct(protected int $count)
    {
        //
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function start(): static
    {
        $this->bar = wpcli_make_progress_bar($this->message, $this->count);

        return $this;
    }

    public function advance(): void
    {
        $this->bar->tick();
    }

    public function finish(): void
    {
        $this->bar->finish();
    }
}
