<?php

namespace Shaferllc\Analytics\Rules;

use Shaferllc\Analytics\Models\Website;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateWebsitePasswordRule implements ValidationRule
{
    /**
     * @var Website
     */
    private Website $website;

    /**
     * Create a new rule instance.
     *
     * @param Website $website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
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
        if ($this->website->password !== $value) {
            $fail(__('The entered password is not correct.'));
        }
    }
}
