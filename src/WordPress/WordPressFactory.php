<?php

namespace Navigator\WordPress;

use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;

class WordPressFactory
{
    /** @param class-string<Post> $model */
    public function registerPostType(string $model): RegisterPostType
    {
        return new RegisterPostType($model, $this);
    }

    /**
     * @param class-string<Term> $model
     */
    public function registerTaxonomy(string $model): RegisterTaxonomy
    {
        return new RegisterTaxonomy($model, $this);
    }
}
