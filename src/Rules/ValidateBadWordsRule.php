<?php

namespace Shaferllc\Analytics\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class ValidateBadWordsRule implements ValidationRule
{
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
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate($attribute, $value, $fail): void
    {
        $lowercaseValue = mb_strtolower($value);

        foreach ($this->bannedWords as $word) {
            if (mb_strpos($lowercaseValue, mb_strtolower($word)) !== false) {
                $fail(__('The :attribute contains a keyword that is banned.'));
                return;
            }
        }
    }
}
