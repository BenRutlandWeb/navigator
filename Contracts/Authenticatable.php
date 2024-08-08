<?php

namespace Navigator\Contracts;

interface Authenticatable
{
    public function authUsername(): string;

    public function authPassword(): string;
}
