<?php

namespace Shaferllc\Analytics\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class ValidateCronjobKeyRule implements ValidationRule
{
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
        if ($value !== config('settings.cronjob_key')) {
            $fail(__('Invalid cron job key.'));
        }
    }
}
