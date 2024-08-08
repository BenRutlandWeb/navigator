<?php

namespace Navigator\WordPress;

use Navigator\Database\Models\Concerns\HasChildren;
use Navigator\Database\Models\Concerns\IsPrivate;
use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;
use Navigator\Database\Relation;
use Navigator\Str\Str;

class RegisterTaxonomy
{
    protected ?string $taxonomy = null;

    protected ?string $postType = null;

    /**
     * @param class-string<Term> $model
     * @param class-string<Post> $postTypeModel
     */
    public function __construct(protected string $model, protected string $postTypeModel, protected WordPressFactory $factory)
    {
        $this->taxonomy = Relation::getObjectType($model);

        $this->postType = Relation::getObjectType($postTypeModel);

        register_taxonomy($this->taxonomy, $this->postType, [
            'labels'            => $this->labels(),
            'hierarchical'      => $this->uses(HasChildren::class),
            'public'            => !$this->uses(IsPrivate::class),
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ]);
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
