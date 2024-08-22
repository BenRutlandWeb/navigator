<?php

namespace Navigator\Foundation;

use Faker\Provider\Base;
use Navigator\Database\Models\User;

class FakerProvider extends Base
{
    public function user(): User
    {
        return $this->generator->randomElement(
            User::all()
        );
    }

    public function taxonomy(): string
    {
        return $this->generator->randomElement(
            get_taxonomies(['public' => true])
        );
    }

    public function taxonomies(int $count = 1, bool $allowDuplicates = false): array
    {
        return $this->generator->randomElements(
            get_taxonomies(['public' => true]),
            $count,
            $allowDuplicates
        );
    }
}
