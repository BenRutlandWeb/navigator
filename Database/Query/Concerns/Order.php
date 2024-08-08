<?php

namespace Navigator\Database\Query\Concerns;

enum Order: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
