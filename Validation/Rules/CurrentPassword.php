<?php

namespace Navigator\Validation\Rules;

class CurrentPassword extends Rule
{
    protected $fillableParams = ['field'];

    public function passes(mixed $value): bool
    {
        $attribute = $this->parameter('field') ?? 'email';

        if ($user = get_user_by('email', $this->validation->getValue($attribute))) {
            return wp_check_password($value, $user->user_pass);
        }

        return false;
    }

    public function getMessage(): string
    {
        return __('The password is incorrect.');
    }

    public function getKey(): string
    {
        return 'current_password';
    }
}
