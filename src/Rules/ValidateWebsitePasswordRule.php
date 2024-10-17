<?php

namespace ShaferLLC\Analytics\Rules;

use ShaferLLC\Analytics\Models\Website;
use Illuminate\Contracts\Validation\Rule;

class ValidateWebsitePasswordRule implements Rule
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
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->website->password === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The entered password is not correct.');
    }
}
