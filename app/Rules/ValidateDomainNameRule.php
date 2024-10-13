<?php

namespace ShaferLLC\Analytics\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateDomainNameRule implements Rule
{
    /**
     * The regular expression pattern for validating domain names.
     *
     * @var string
     */
    private const DOMAIN_PATTERN = '/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$|^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}$/i';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match(self::DOMAIN_PATTERN, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Invalid domain name.');
    }
}
