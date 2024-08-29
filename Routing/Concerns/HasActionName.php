<?php

namespace Navigator\Routing\Concerns;

use Closure;

trait HasActionName
{
    public function getActionName(): string
    {
        if ($this->callback instanceof Closure) {
            return 'Closure';
        }

        if (is_string($this->callback)) {
            return $this->callback;
        }

        if (is_object($this->callback)) {
            return get_class($this->callback);
        }

        if (is_array($this->callback)) {
            $class =  is_string($this->callback[0])
                ? $this->callback[0]
                : get_class($this->callback[0]);

            return $class . '::' . $this->callback[1] . '()';
        }

        return '';
    }
}
