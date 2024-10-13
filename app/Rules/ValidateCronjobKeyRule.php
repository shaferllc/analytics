<?php

namespace ShaferLLC\Analytics\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateCronjobKeyRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value === config('settings.cronjob_key');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Invalid cron job key.');
    }
}
