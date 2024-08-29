<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Navigator\Collections\Collection;
use Navigator\Contracts\Authenticatable;
use Navigator\Contracts\MailableInterface;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\UserBuilder;
use Navigator\Mail\Concerns\Notifiable;
use WP_User;

class User implements Authenticatable, MailableInterface, ModelInterface
{
    use HasRelationships;
    use HasMeta;
    use Notifiable;
    use InteractsWithAttributes;

    public function __construct(readonly public WP_User $object)
    {
        //
    }

    /** @return UserBuilder<static> */
    public static function query(): UserBuilder
    {
        $query = new UserBuilder(static::class);

        $query->where('fields', 'all');

        static::withGlobalScopes($query);

        return $query;
    }

    public static function withGlobalScopes(UserBuilder $query): void
    {
        //
    }

    public static function find(int $id): ?static
    {
        if ($user = get_user_by('ID', $id)) {
            return new static($user);
        }

        return null;
    }

    /** @return Collection<int, static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    public function id(): int
    {
        return $this->object->ID;
    }

    public function createdAt(): Carbon
    {
        return Carbon::create($this->user_registered);
    }

    public function email(): string
    {
        return $this->user_email;
    }

    public function name(): ?string
    {
        return $this->display_name;
    }

    public function authUsername(): string
    {
        return $this->email();
    }

    public function authPassword(): string
    {
        return $this->user_pass;
    }

    public function hasRole(string|array $roles): bool
    {
        foreach ((array) $roles as $role) {
            if (in_array($role, $this->roles())) {
                return true;
            }
        }

        return false;
    }

    public function roles(): array
    {
        return $this->object->roles;
    }

    public function addRole(string $role): static
    {
        $this->object->add_role($role);

        return $this;
    }

    public function setRole(string $role): static
    {
        $this->object->set_role($role);

        return $this;
    }

    public function removeRole(string $role): static
    {
        $this->object->remove_role($role);

        return $this;
    }

    public function can(string $capability, mixed ...$args): bool
    {
        return $this->object->has_cap($capability, ...$args);
    }

    public function cannot(string $capability, mixed ...$args): bool
    {
        return !$this->can($capability, ...$args);
    }

    public function canAny(array $capabilities, mixed ...$args): bool
    {
        foreach ($capabilities as $capability) {
            if ($this->can($capability, ...$args)) {
                return true;
            };
        }

        return false;
    }

    public function addCapability(string $capability): void
    {
        $this->object->add_cap($capability);
    }

    public function removeCapability(string $capability): void
    {
        $this->object->remove_cap($capability);
    }

    public static function create(array $attributes = []): static
    {
        $id = wp_insert_user($attributes);

        if (!is_wp_error($id)) {
            return static::find($id);
        }

        return null;
    }

    public function update(array $attributes = []): bool
    {
        $attributes['ID'] = $this->id();

        $return = wp_update_user($attributes);

        return is_wp_error($return) ? false : true;
    }

    public function delete(): bool
    {
        require_once(ABSPATH . 'wp-admin/includes/user.php');

        return wp_delete_user($this->id());
    }
}
