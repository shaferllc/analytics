<?php

namespace ShaferLLC\Analytics\Http\Requests\API;

use ShaferLLC\Analytics\Http\Requests\UpdateWebsiteRequest as BaseUpdateWebsiteRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateWebsiteRequest extends BaseUpdateWebsiteRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors(),
                'status' => 422
            ], 422)
        );
    }
}
