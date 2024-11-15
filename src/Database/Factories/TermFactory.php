<?php

namespace Navigator\Database\Factories;

use Navigator\Database\Models\Term;
use Navigator\Database\Relation;
use WP_Term;

/**
 * @template T of Term
 * @extends Factory<T>
 */
class TermFactory extends Factory
{
    /** @return Term */
    public function newModel(array $attributes = []): Term
    {
        $model = $this->model;

        return new $model(new WP_Term((object) $attributes));
    }

    public function definition(): array
    {
        $name = $this->faker->text(15);

        return [
            'term_id'     => $this->faker->numberBetween(1, 100),
            'name'        => $name,
            'slug'        => sanitize_title($name),
            'taxonomy'    => Relation::getObjectType($this->model),
            'description' => $this->faker->paragraph(1),
            'parent'      => $this->faker->numberBetween(1, 100),
        ];
    }
}
