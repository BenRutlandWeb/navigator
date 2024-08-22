<?php

namespace Navigator\Validation\Rules;

class EmailExists extends Rule
{
    public function passes(mixed $value): bool
    {
        return email_exists($value) ? true : false;
    }

    public function getMessage(): string
    {
        return __('The selected :attribute is invalid.');
    }

    public function getKey(): string
    {
        return 'email_exists';
    }
}
