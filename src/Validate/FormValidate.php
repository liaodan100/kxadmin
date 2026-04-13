<?php

namespace KxAdmin\Validate;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class FormValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $error = $validator->errors();

        throw new HttpResponseException(response()->json([
            'data' => [
                'field' => $error->keys()[0] ?? null,
                'message' => $error->first(),
            ],
            'message' => $error->first(),
            'code' => 422,
        ], 422));
    }
}
