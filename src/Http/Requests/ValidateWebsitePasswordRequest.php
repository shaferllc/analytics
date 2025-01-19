<?php

namespace Shaferllc\Analytics\Http\Requests;

use Shaferllc\Analytics\Rules\ValidateWebsitePasswordRule;
use Shaferllc\Analytics\Models\Website;
use Illuminate\Foundation\Http\FormRequest;

class ValidateWebsitePasswordRequest extends FormRequest
{
    /**
     * @var Website
     */
    protected $site;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->site = Website::firstWhere('domain', $this->route('id'));

        return $this->site !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', new ValidateWebsitePasswordRule($this->site)]
        ];
    }
}
