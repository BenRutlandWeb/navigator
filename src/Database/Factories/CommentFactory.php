<?php

namespace Navigator\Database\Factories;

use DateTimeZone;
use Navigator\Database\Models\Comment;
use WP_Comment;

/**
 * @template T of Comment
 * @extends Factory<T>
 */
class CommentFactory extends Factory
{
    /** @return Comment */
    public function newModel(array $attributes = []): Comment
    {
        $model = $this->model;

        return new $model(new WP_Comment((object) $attributes));
    }

    public function definition(): array
    {
        $date = $this->faker->dateTime();

        [$tz, $format] = [new DateTimeZone('Europe/London'), 'Y-m-d H:i:s'];

        return [
            'comment_ID'           => $this->faker->numberBetween(1, 100),
            'comment_post_ID'      => $this->faker->numberBetween(1, 100),
            'comment_author'       => $this->faker->name(),
            'comment_author_email' => $this->faker->email(),
            'comment_author_url'   => $this->faker->url(),
            'comment_author_IP'    => $this->faker->ipv4(),
            'comment_date'         => $date->format($format),
            'comment_date_gmt'     => $date->setTimezone($tz)->format($format),
            'comment_content'      => $this->faker->paragraph(),
            'comment_approved'     => $this->faker->boolean(),
            'comment_type'         => 'comment',
            'comment_parent'       => $this->faker->numberBetween(1, 100),
            'user_id'              => $this->faker->numberBetween(1, 50),
        ];
    }
}
