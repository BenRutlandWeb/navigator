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
     * @param class-string<Term> $termModel
     * @param class-string<Post> $postTypeModel
     */
    public function registerTaxonomy(string $termModel, string $postTypeModel): RegisterTaxonomy
    {
        return new RegisterTaxonomy($termModel, $postTypeModel, $this);
    }
}
