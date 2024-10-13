<?php

namespace ShaferLLC\Analytics\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ValidateUserPasswordRule implements Rule
{
    /**
     * The hashed password to be checked against.
     *
     * @var string
     */
    private string $hashedPassword;

    /**
     * Create a new rule instance.
     *
     * @param string $hashedPassword
     */
    public function __construct(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Hash::check($value, $this->hashedPassword);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The current password is not correct.');
    }
}
