<?php

namespace Navigator\Hashing;

enum Hash: string
{
    case BCRYPT = 'bcrypt';
    case HMAC = 'hmac';
}
