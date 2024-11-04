<?php

namespace Shaferllc\Analytics\Rules;

use Shaferllc\Analytics\Models\Website;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateWebsiteURLRule implements ValidationRule
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
        $url = $this->extractHost($value);
        if ($url === false || Website::where('domain', $url)->exists()) {
            $fail(__('This domain is already being used.'));
        }
    }

    /**
     * Extract the host from a URL.
     *
     * @param  string  $value
     * @return string|false
     */
    private function extractHost($value)
    {
        $value = str_replace('://www.', '://', $value);
        $parsedUrl = parse_url($value);
        return isset($parsedUrl['host']) ? $parsedUrl['host'] : false;
    }
}
