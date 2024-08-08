<?php

namespace Navigator\Contracts;

interface MailableInterface
{
    public function email(): string;

    public function name(): ?string;
}
