<?php

namespace ShaferLLC\Analytics\Rules;

use ShaferLLC\Analytics\Models\Website;
use Illuminate\Contracts\Validation\Rule;

class ValidateWebsiteURLRule implements Rule
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
        $url = $this->extractHost($value);
        return $url !== false && !Website::where('domain', $url)->exists();
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

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('This domain is already being used.');
    }
}
