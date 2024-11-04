<?php

namespace Shaferllc\Analytics\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class ValidateDomainNameRule implements ValidationRule
{
    /**
     * The regular expression pattern for validating domain names.
     *
     * @var string
     */
    private const DOMAIN_PATTERN = '/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$|^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}$/i';

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
        if (!preg_match(self::DOMAIN_PATTERN, $value)) {
            $fail(__('Invalid domain name.'));
        }
    }
}
