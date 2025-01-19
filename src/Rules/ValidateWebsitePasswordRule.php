<?php

namespace Shaferllc\Analytics\Rules;

use Shaferllc\Analytics\Models\Website;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateWebsitePasswordRule implements ValidationRule
{
    /**
     * @var Website
     */
    private Website $site;

    /**
     * Create a new rule instance.
     *
     * @param Website $site
     */
    public function __construct(Website $site)
    {
        $this->site = $site;
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
        if ($this->site->password !== $value) {
            $fail(__('The entered password is not correct.'));
        }
    }
}
