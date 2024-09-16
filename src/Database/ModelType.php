<?php

namespace Navigator\Database;

enum ModelType: string
{
    case POST    = 'post';
    case TERM    = 'term';
    case COMMENT = 'comment';
    case USER    = 'user';
}
