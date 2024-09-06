<?php

namespace Navigator\Validation;

use Navigator\Validation\Rules\Enum;
use Rakit\Validation\Rule as BaseRule;
use Rakit\Validation\Rules\In;

class Rule
{
    public static function enum(string $type): BaseRule
    {
        $rule = new Enum();

        $rule->setKey('enum');

        $rule->setParameter('type', $type);

        return $rule;
    }

    public static function in(array $options): BaseRule
    {
        $rule = new In();

        $rule->setKey('in');

        $rule->fillParameters($options);

        return $rule;
    }
}
