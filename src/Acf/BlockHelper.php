<?php

namespace Navigator\Acf;

use Navigator\Database\Models\Post;
use Navigator\Database\Relation;

class BlockHelper
{
    protected Post $object;

    protected array $fields = [];

    public function __construct(public array $block, protected int $post_id, protected bool $preview)
    {
        $model = Relation::getMorphedModel(get_post_type($post_id));

        $this->object = $model::find($post_id);

        $this->fields = get_fields() ?: [];
    }

    public function attributes(array $attributes = []): string
    {
        return $this->preview ? '' : get_block_wrapper_attributes($attributes);
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function hasField(string $key): bool
    {
        return isset($this->fields[$key]);
    }

    public function field(string $key, mixed $default = null): mixed
    {
        return $this->fields[$key] ?? $default;
    }

    public function __isset(string $key): bool
    {
        return $this->hasField($key);
    }

    public function __get(string $key): mixed
    {
        return $this->field($key);
    }
}
