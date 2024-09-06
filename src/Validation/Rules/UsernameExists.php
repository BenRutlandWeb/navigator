<?php

namespace Navigator\Validation\Rules;

class UsernameExists extends Rule
{
    public function passes(mixed $value): bool
    {
        return username_exists($value) ? true : false;
    }

    public function getMessage(): string
    {
        return __('The selected :attribute is invalid.');
    }

    public function getKey(): string
    {
        return 'username_exists';
    }
}
