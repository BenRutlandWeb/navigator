<?php

namespace Navigator\Validation\Exceptions;

use Exception;
use Navigator\Http\JsonResponse;
use Navigator\Validation\Validator;

class ValidationException extends Exception
{
    public function __construct(protected Validator $validator)
    {
        //
    }

    public function getErrors(): array
    {
        return $this->validator->getErrors();
    }

    public function getResponse(): JsonResponse
    {
        return new JsonResponse(['errors' => $this->getErrors()], 422);
    }
}
