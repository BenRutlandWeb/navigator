<?php

namespace Navigator\Acf;

use Navigator\Acf\Models\Concerns\HasAcfFields;
use Navigator\Database\Models\Post;
use Navigator\Database\Relation;

class BlockHelper implements BlockInterface
{
    use HasAcfFields;

    protected ?Post $object;

    public function __construct(public array $block, protected int $post_id, protected bool $preview)
    {
        $model = Relation::getMorphedModel(get_post_type($post_id));

        $this->object = $model ? $model::find($post_id) : null;
    }

    public function attributes(array $attributes): string
    {
        $string = '';

        foreach ($attributes as $attr => $value) {
            if (trim($value)) {
                $string .= sprintf('%s="%s" ', $attr, $value);
            }
        }

        return trim($string);
    }

    public function id(): string
    {
        return $this->block['id'];
    }
}
