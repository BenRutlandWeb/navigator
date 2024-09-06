<?php

namespace Navigator\Validation\Rules;

use TypeError;

class Enum extends Rule
{
    protected $fillableParams = ['type'];

    public function passes(mixed $value): bool
    {
        $this->requireParameters(['type']);

        $type = $this->parameter('type');

        if (!enum_exists($type) || !method_exists($type, 'tryFrom')) {
            return false;
        }

        try {
            return !is_null($type::tryFrom($value));
        } catch (TypeError $e) {
            return false;
        }
    }

    public function getMessage(): string
    {
        return __('The selected :attribute is invalid.');
    }

    public function getKey(): string
    {
        return 'enum';
    }
}
