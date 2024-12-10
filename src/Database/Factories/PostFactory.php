<?php

namespace Navigator\Database\Factories;

use DateTimeZone;
use Navigator\Database\Models\Post;
use Navigator\Database\Relation;
use WP_Post;

/**
 * @template T of Post
 * @extends Factory<T>
 */
class PostFactory extends Factory
{
    /** @return Post */
    public function newModel(array $attributes = []): Post
    {
        $model = $this->model;

        return new $model(new WP_Post((object) $attributes));
    }

    public function definition(): array
    {
        $title = $this->faker->text(50);
        $date = $this->faker->dateTime();
        $modified = $this->faker->dateTime();
        [$tz, $format] = [new DateTimeZone('Europe/London'), 'Y-m-d H:i:s'];

        return [
            'ID'                => $this->faker->numberBetween(1, 100),
            'post_author'       => $this->faker->numberBetween(1, 50),
            'post_date'         => $date->format($format),
            'post_date_gmt'     => $date->setTimezone($tz)->format($format),
            'post_content'      => $this->faker->paragraph(),
            'post_title'        => $title,
            'post_excerpt'      => $this->faker->paragraph(1),
            'post_status'       => $this->faker->randomElement(['publish', 'draft']),
            'post_name'         => sanitize_title($title),
            'post_modified'     => $modified->format($format),
            'post_modified_gmt' => $modified->setTimezone($tz)->format($format),
            'post_parent'       => $this->faker->numberBetween(1, 100),
            'post_type'         => Relation::getObjectType($this->model),
        ];
    }

    public function published(): static
    {
        return $this->with(['post_status' => 'publish']);
    }

    public function draft(): static
    {
        return $this->with(['post_status' => 'draft']);
    }
}
