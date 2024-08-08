<?php

namespace Navigator\Auth;

use Navigator\Database\Models\User;
use Navigator\Database\Relation;

class Auth
{
    protected ?User $user = null;

    public function id(): ?int
    {
        return ($id = get_current_user_id()) !== 0 ? $id : null;
    }

    public function user(): ?User
    {
        if ($this->user) {
            return $this->user;
        }

        if ($id = $this->id()) {
            return $this->user = $this->retrieveById($id);
        }

        return null;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        wp_set_current_user($user->id());

        return $this;
    }

    /** @return class-string<User> */
    public function resolveUserModel(): string
    {
        return Relation::getMorphedModel('user') ?? User::class;
    }

    public function retrieveById(int $id): ?User
    {
        $model = $this->resolveUserModel();

        return $model::find($id);
    }

    public function retrieveByCredentials(array $credentials): ?User
    {
        $user = wp_authenticate($credentials['email'] ?? '', $credentials['password'] ?? '');

        if (!is_wp_error($user)) {
            return $this->retrieveById($user->ID);
        }

        return null;
    }

    public function validateCredentials(User $user, array $credentials): bool
    {
        return wp_check_password(
            $credentials['password'] ?? '',
            $user->authPassword(),
            $user->id()
        );
    }

    public function check(): bool
    {
        return is_user_logged_in();
    }

    public function validate(array $credentials = []): bool
    {
        return $this->retrieveByCredentials($credentials) ? true : false;
    }

    public function attempt(array $credentials = [], bool $remember = false): bool
    {
        if ($user = $this->retrieveByCredentials($credentials)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    public function login(User $user, bool $remember = false): void
    {
        $id = $user->id();

        wp_clear_auth_cookie();
        wp_set_auth_cookie($id, $remember);

        $this->setUser($user);
    }

    public function loginUsingId(int $id, bool $remember = false): bool
    {
        if ($user = $this->retrieveById($id)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    public function once(array $credentials = []): bool
    {
        if ($user = $this->retrieveByCredentials($credentials)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    public function onceUsingId(int $id): bool
    {
        if ($user = $this->retrieveById($id)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $this->user = null;

        wp_logout();
    }
}
