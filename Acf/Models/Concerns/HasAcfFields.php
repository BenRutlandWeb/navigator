<?php

namespace Navigator\Acf\Models\Concerns;

use Navigator\Acf\Models\AcfField;

trait HasAcfFields
{
    protected static ?AcfField $acf = null;

    public function acf(): AcfField
    {
        if (!static::$acf) {
            static::$acf = new AcfField($this);
        }

        return static::$acf;
    }
}
