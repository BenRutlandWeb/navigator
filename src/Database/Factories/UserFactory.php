<?php

namespace Navigator\Database\Factories;

use Navigator\Database\Models\User;
use WP_User;

/**
 * @template T of ModelInterface
 * @extends Factory<T>
 */
class UserFactory extends Factory
{
    /** @return User */
    public function newModel(array $attributes = []): User
    {
        $model = $this->model;

        return new $model(new WP_User((object) $attributes));
    }

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'ID'              => $this->faker->numberBetween(1, 50),
            'user_login'      => sanitize_user($name),
            'user_pass'       => wp_hash_password($this->faker->password()),
            'user_nicename'   => sanitize_title($name),
            'user_email'      => $this->faker->email(),
            'user_url'        => $this->faker->url(),
            'user_registered' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'display_name'    => $name,
        ];
    }
}
