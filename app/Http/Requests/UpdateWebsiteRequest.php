<?php

namespace ShaferLLC\Analytics\Http\Requests;

use ShaferLLC\Analytics\Models\Website;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->has('user_id') && !$this->user()->isAdmin()) {
            return false;
        }

        if ($this->has('user_id')) {
            Website::where('id', $this->route('id'))
                   ->where('user_id', $this->input('user_id'))
                   ->firstOrFail();
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $privacyRule = Rule::in([0, 1, 2]);

        return [
            'privacy' => ['sometimes', 'required', 'integer', $privacyRule],
            'password' => [
                $this->input('privacy') < 2 ? 'nullable' : 'sometimes',
                'string',
                'min:1',
                'max:128'
            ],
            'exclude_bots' => ['sometimes', 'boolean'],
            'exclude_ips' => ['sometimes', 'nullable', 'string'],
            'exclude_params' => ['sometimes', 'nullable', 'string'],
            'email' => ['sometimes', 'nullable', 'integer']
        ];
    }
}
