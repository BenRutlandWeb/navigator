<?php

namespace Navigator\Validation\Rules;

class UniqueUsername extends Rule
{
    public function passes(mixed $value): bool
    {
        return username_exists($value) ? false : true;
    }

    public function getMessage(): string
    {
        return __('The :attribute has already been taken.');
    }

    public function getKey(): string
    {
        return 'unique_username';
    }
}
