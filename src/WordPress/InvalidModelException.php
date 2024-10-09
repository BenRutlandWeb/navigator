<?php

namespace Navigator\WordPress;

use InvalidArgumentException;
use Navigator\Database\ModelInterface;

class InvalidModelException extends InvalidArgumentException
{
    /** @param class-string<ModelInterface> $model */
    public function __construct(string $model, string $class)
    {
        parent::__construct("{$model} is an invalid model for {$class}.");
    }
}
