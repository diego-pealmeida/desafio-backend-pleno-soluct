<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

abstract class BaseRequest extends FormRequest
{
    use ApiResponse;

    /**
     * @response 422 {
     *  "errors": [
     *      "string",
     *      "string"
     *  ]
     * }
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = collect($validator->errors()->messages())->flatten();

            throw new HttpResponseException(
                response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        parent::failedValidation($validator);
    }
}
