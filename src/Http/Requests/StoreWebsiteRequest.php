<?php

namespace ShaferLLC\Analytics\Http\Requests;

use ShaferLLC\Analytics\Rules\{ValidateBadWordsRule, ValidateDomainNameRule, WebsiteLimitGateRule};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', mb_strtolower($this->input('domain')));
        $this->merge(['domain' => $domain]);
    }

    public function rules(): array
    {
        return [
            'domain' => [
                'required',
                'max:255',
                new ValidateDomainNameRule(),
                'unique:websites,domain',
                new ValidateBadWordsRule(),
                new WebsiteLimitGateRule($this->user())
            ],
            'privacy' => ['nullable', 'integer', 'between:0,2'],
            'password' => [
                Rule::requiredIf(fn() => $this->input('privacy') == 2),
                'nullable',
                'string',
                'min:1',
                'max:128'
            ],
            'exclude_bots' => ['nullable', 'boolean'],
            'exclude_params' => ['nullable', 'string'],
            'exclude_ips' => ['nullable', 'string'],
            'email' => ['nullable', 'integer']
        ];
    }
}
