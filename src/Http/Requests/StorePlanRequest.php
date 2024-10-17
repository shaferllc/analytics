<?php

namespace ShaferLLC\Analytics\Http\Requests;

use ShaferLLC\Analytics\Rules\ValidateExtendedLicenseRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64'],
            'description' => ['required', 'string', 'max:256'],
            'amount_month' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'amount_year' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'currency' => ['required', 'string'],
            'coupons' => ['sometimes', 'nullable', 'array'],
            'tax_rates' => ['sometimes', 'nullable', 'array'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'visibility' => ['required', 'integer', Rule::in([0, 1])],
            'position' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'features.api' => ['required', 'integer', Rule::in([0, 1])]
        ];
    }
}
