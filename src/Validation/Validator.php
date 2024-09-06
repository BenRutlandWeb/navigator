<?php

namespace Navigator\Validation;

use Navigator\Validation\Exceptions\ValidationException;
use Rakit\Validation\Validation;

class Validator
{
    public function __construct(protected Validation $validation)
    {
        //
    }

    public function validate(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this);
        }

        return $this->validated();
    }

    public function passes(): bool
    {
        $this->validation->validate();

        return $this->validation->passes();
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function validated(): array
    {
        $this->validation->validate();

        return $this->validation->getValidData();
    }

    public function getErrors(): array
    {
        return $this->validation->errors()->toArray();
    }
}
