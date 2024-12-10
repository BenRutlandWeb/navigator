<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Generator;
use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Contracts\Authenticatable;
use Navigator\Contracts\MailableInterface;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\Factories\UserFactory;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\UserBuilder;
use Navigator\Notifications\Concerns\Notifiable;
use Navigator\Pagination\Paginator;
use WP_User;

/**
 * @property-read int $ID
 * @property-read string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property string $display_name
 */
class User implements Authenticatable, MailableInterface, ModelInterface
{
    use HasMeta;
    use HasRelationships;
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
        return static::query()->include([$id])->first();
    }

    public static function findOrFail(int $id): ?static
    {
        return static::find($id) ?? throw new ModelNotFoundException(static::class);
    }

    /** @return Collection<int, static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /** @param (callable(Collection<int, static>, int): mixed) $callback */
    public static function chunk(int $count, callable $callback): bool
    {
        return static::query()->chunk($count, $callback);
    }

    /** @return Paginator<static> */
    public static function paginate(int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator
    {
        return static::query()->paginate($perPage, $pageName, $page, $total);
    }

    /** @return Generator<static> */
    public static function lazy(int $chunk = 1000): Generator
    {
        return static::query()->lazy($chunk);
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
            if (Arr::has($role, $this->roles())) {
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

    public function canAll(array $capabilities, mixed ...$args): bool
    {
        foreach ($capabilities as $capability) {
            if ($this->cannot($capability, ...$args)) {
                return false;
            };
        }

        return true;
    }

    public function addCapability(string $capability): void
    {
        $this->object->add_cap($capability);
    }

    public function removeCapability(string $capability): void
    {
        $this->object->remove_cap($capability);
    }

    public static function create(array $attributes): static
    {
        unset($attributes['ID']);

        $id = wp_insert_user($attributes);

        if (!is_wp_error($id)) {
            return static::find($id);
        }

        return null;
    }

    public function update(array $attributes): bool
    {
        $attributes['ID'] = $this->id();

        return $this->fill($attributes)->save();
    }

    public function save(): bool
    {
        $return = wp_update_user($this->toArray());

        return !is_wp_error($return);
    }

    public function delete(): bool
    {
        return static::destroy($this->id()) ? true : false;
    }

    /** @param int|array<int, int> $ids */
    public static function destroy(int|array $ids): int
    {
        require_once(ABSPATH . 'wp-admin/includes/user.php');

        $affectedRows = 0;

        foreach ((array) $ids as $id) {
            if (wp_delete_user($id)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public static function factory(): UserFactory
    {
        return new UserFactory(static::class);
    }
}
