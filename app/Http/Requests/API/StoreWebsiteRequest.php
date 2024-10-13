<?php

namespace ShaferLLC\Analytics\Http\Requests\API;

use ShaferLLC\Analytics\Http\Requests\StoreWebsiteRequest as BaseStoreWebsiteRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreWebsiteRequest extends BaseStoreWebsiteRequest
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
