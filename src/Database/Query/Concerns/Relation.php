<?php

namespace Navigator\Database\Query\Concerns;

enum Relation: string
{
    case AND = 'AND';
    case OR = 'OR';
}
