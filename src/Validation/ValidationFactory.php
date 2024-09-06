<?php

namespace Navigator\Validation;

use Rakit\Validation\Validator as BaseValidator;

class ValidationFactory
{
    public function __construct(protected BaseValidator $validator)
    {
        //
    }

    public function make(array $input = [], array $rules = [], array $messages = []): Validator
    {
        return new Validator(
            $this->validator->make($input, $rules, $messages)
        );
    }
}
