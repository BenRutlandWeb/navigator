<?php

namespace Navigator\Acf;

abstract class FieldGroup
{
    public function __construct()
    {
        //
    }

    public function register(): void
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group($this->schema());
        }
    }

    abstract public function schema(): array;
}
