<?php

namespace Navigator\WordPress;

use Navigator\Database\Models\Concerns\HasChildren;
use Navigator\Database\Models\Concerns\IsPrivate;
use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;
use Navigator\Database\Relation;
use Navigator\Str\Str;
use Navigator\WordPress\Concerns\ValidatesModels;

class RegisterTaxonomy
{
    use ValidatesModels;

    protected ?string $taxonomy = null;

    /**
     * @param class-string<Term> $model
     */
    public function __construct(protected string $model, protected WordPressFactory $factory)
    {
        $this->validateModel($model, Term::class);

        $this->taxonomy = Relation::getObjectType($model);

        $hierarchical = $this->uses(HasChildren::class);
        $private = $this->uses(IsPrivate::class);

        register_taxonomy($this->taxonomy, [], [
            'labels'            => $this->labels(),
            'hierarchical'      => $hierarchical,
            'public'            => !$private,
            'show_admin_column' => !$private,
            'show_in_rest'      => !$private,
        ]);
    }

    /** @param class-string<Post> $model */
    public function withPostType(string $model): static
    {
        $this->validateModel($model, Post::class);

        $postType = Relation::getObjectType($model);

        if (!post_type_exists($postType)) {
            $this->factory->registerPostType($model);
        }

        register_taxonomy_for_object_type($this->taxonomy, $postType);

        return $this;
    }

    public function labels(): array
    {
        $singular = Str::of($this->model)->classBasename()->headline();
        $plural =  $singular->plural();
        $singularLower = $singular->lower();

        return [
            'name'                       => sprintf(_x('%s', 'taxonomy general name', 'theme'), $plural),
            'singular_name'              => sprintf(_x('%s', 'taxonomy singular name', 'theme'), $singular),
            'search_items'               => sprintf(__('Search %s', 'theme'), $plural),
            'popular_items'              => sprintf(__('Popular %s', 'theme'), $plural),
            'all_items'                  => sprintf(__('All %s', 'theme'), $plural),
            'parent_item'                => sprintf(__('Parent %s', 'theme'), $singularLower),
            'parent_item_colon'          => sprintf(__('Parent %s:', 'theme'), $singularLower),
            'edit_item'                  => sprintf(__('Edit %s', 'theme'), $singular),
            'update_item'                => sprintf(__('Update %s', 'theme'), $singular),
            'add_new_item'               => sprintf(__('Add New %s', 'theme'), $singular),
            'new_item_name'              => sprintf(__('New %s Name', 'theme'), $singular),
            'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'theme'), $singularLower),
            'add_or_remove_items'        => sprintf(__('Add or remove %s', 'theme'), $singularLower),
            'choose_from_most_used'      => sprintf(__('Choose from the most used %s', 'theme'), $singularLower),
            'not_found'                  => sprintf(__('No %s found.', 'theme'), $singularLower),
            'menu_name'                  => sprintf(__('%s', 'theme'), $plural),
        ];
    }

    public function uses(string $trait): bool
    {
        return in_array($trait, class_uses($this->model));
    }
}
