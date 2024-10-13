<?php

namespace ShaferLLC\Analytics\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateBadWordsRule implements Rule
{
    /**
     * The input attribute
     *
     * @var string
     */
    private $attribute;

    /**
     * The banned words array
     *
     * @var array
     */
    private $bannedWords;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bannedWords = preg_split('/\n|\r/', config('settings.bad_words'), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $lowercaseValue = mb_strtolower($value);

        foreach ($this->bannedWords as $word) {
            if (mb_strpos($lowercaseValue, mb_strtolower($word)) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute contains a keyword that is banned.', ['attribute' => $this->attribute]);
    }
}
