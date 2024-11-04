<?php

namespace Shaferllc\Analytics\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class ValidateUserPasswordRule implements ValidationRule
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
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate($attribute, $value, $fail): void
    {
        if (!Hash::check($value, $this->hashedPassword)) {
            $fail(__('The current password is not correct.'));
        }
    }
}
