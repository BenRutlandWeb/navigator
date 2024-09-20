<?php

namespace Navigator\Database\Exceptions;

use Navigator\Database\ModelInterface;
use Navigator\Http\Exceptions\HttpException;

class ModelNotFoundException extends HttpException
{
    /** @param class-string<ModelInterface> $model */
    public function __construct(string $model)
    {
        parent::__construct(404, "No query results for model [{$model}].");
    }
}
