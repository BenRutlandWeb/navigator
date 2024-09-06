<?php

namespace Navigator\Validation\Rules;

use Rakit\Validation\Rule as BaseRule;

abstract class Rule extends BaseRule
{
    abstract public function passes(mixed $value): bool;

    public function check(mixed $value): bool
    {
        return $this->passes($value) ? true : false;
    }
}
