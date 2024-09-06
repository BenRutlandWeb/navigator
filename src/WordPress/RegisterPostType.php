<?php

namespace Navigator\WordPress;

use Navigator\Collections\Arr;
use Navigator\Database\Models\Concerns\HasAuthor;
use Navigator\Database\Models\Concerns\HasChildren;
use Navigator\Database\Models\Concerns\HasComments;
use Navigator\Database\Models\Concerns\HasContent;
use Navigator\Database\Models\Concerns\HasExcerpt;
use Navigator\Database\Models\Concerns\HasFeaturedImage;
use Navigator\Database\Models\Concerns\HasPostFormat;
use Navigator\Database\Models\Concerns\HasRevisions;
use Navigator\Database\Models\Concerns\HasTitle;
use Navigator\Database\Models\Concerns\IsPrivate;
use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;
use Navigator\Database\Relation;
use Navigator\Str\Str;

class RegisterPostType
{
    protected ?string $postType = null;

    /** @param class-string<Post> $model */
    public function __construct(protected string $model, protected WordPressFactory $factory)
    {
        $this->postType = Relation::getObjectType($model);

        register_post_type($this->postType, [
            'labels'       => $this->labels(),
            'menu_icon'    => $model::dashicon()->withPrefix(),
            'public'       => !$this->uses(IsPrivate::class),
            'hierarchical' => $hierarchical = $this->uses(HasChildren::class),
            'has_archive'  => !$hierarchical,
            'supports'     => $this->supports($model) ?: false,
            'show_in_rest' => true,
        ]);

        return $this;
    }

    /** @param class-string<Term> $model */
    public function withTaxonomy(string $model): static
    {
        $taxonomy = Relation::getObjectType($model);

        if (!taxonomy_exists($taxonomy)) {
            $this->factory->registerTaxonomy($model, $this->model);
        }

        register_taxonomy_for_object_type($taxonomy, $this->postType);

        return $this;
    }

    /** @param class-string<Post> $post */
    public function supports(): array
    {
        $uses = class_uses($this->model);

        return Arr::filter([
            in_array(HasTitle::class, $uses) ? 'title' : null,
            in_array(HasContent::class, $uses) ? 'editor' : null,
            in_array(HasComments::class, $uses) ? 'comments' : null,
            in_array(HasRevisions::class, $uses) ? 'revisions' : null,
            // 'trackbacks',
            in_array(HasAuthor::class, $uses) ? 'author' : null,
            in_array(HasExcerpt::class, $uses) ? 'excerpt' : null,
            in_array(HasChildren::class, $uses) ? 'page-attributes' : null,
            in_array(HasFeaturedImage::class, $uses) ? 'thumbnail' : null,
            // 'custom-fields',
            in_array(HasPostFormat::class, $uses) ? 'post-formats' : null,
        ]);
    }

    public function labels(): array
    {
        $singular = Str::of($this->model)->classBasename()->headline();
        $plural =  $singular->plural();
        $singularLower = $singular->lower();
        $pluralLower = $plural->lower();

        return [
            'name'                  => sprintf(_x('%s', 'Post type general name', 'theme'), $plural),
            'singular_name'         => sprintf(_x('%s', 'Post type singular name', 'theme'), $singular),
            'menu_name'             => sprintf(_x('%s', 'Admin Menu text', 'theme'), $plural),
            'name_admin_bar'        => sprintf(_x('%s', 'Add New on Toolbar', 'theme'), $singular),
            'add_new'               => sprintf(__('Add New', 'theme'), $singular),
            'add_new_item'          => sprintf(__('Add New %s', 'theme'), $singularLower),
            'new_item'              => sprintf(__('New %s', 'theme'), $singularLower),
            'edit_item'             => sprintf(__('Edit %s', 'theme'), $singularLower),
            'view_item'             => sprintf(__('View %s', 'theme'), $singularLower),
            'all_items'             => sprintf(__('All %s', 'theme'), $pluralLower),
            'search_items'          => sprintf(__('Search %s', 'theme'), $pluralLower),
            'parent_item_colon'     => sprintf(__('Parent %s:', 'theme'), $pluralLower),
            'not_found'             => sprintf(__('No %s found.', 'theme'), $pluralLower),
            'not_found_in_trash'    => sprintf(__('No %s found in Trash.', 'theme'), $pluralLower),
            'featured_image'        => sprintf(_x('%s Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'theme'), $singular),
            'set_featured_image'    => sprintf(_x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'theme'), $singular),
            'remove_featured_image' => sprintf(_x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'theme'), $singular),
            'use_featured_image'    => sprintf(_x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'theme'), $singular),
            'archives'              => sprintf(_x('%s archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'theme'), $singular),
            'insert_into_item'      => sprintf(_x('Insert into %s', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'theme'), $singularLower),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'theme'), $singularLower),
            'filter_items_list'     => sprintf(_x('Filter %s list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'theme'), $pluralLower),
            'items_list_navigation' => sprintf(_x('%s list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'theme'), $plural),
            'items_list'            => sprintf(_x('%s list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'theme'), $plural),
        ];
    }

    public function uses(string $trait): bool
    {
        return in_array($trait, class_uses($this->model));
    }
}
