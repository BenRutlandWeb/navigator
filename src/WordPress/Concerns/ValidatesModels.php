<?php

namespace Navigator\WordPress\Concerns;

use Navigator\Database\ModelInterface;
use Navigator\WordPress\InvalidModelException;

trait ValidatesModels
{
    /**
     * @param class-string<ModelInterface> $model
     * @param class-string<ModelInterface> $type
     */
    public function validateModel(string $model, string $type): void
    {
        if (!is_a($model, $type, true)) {
            throw new InvalidModelException($model, get_class($this));
        }
    }
}
