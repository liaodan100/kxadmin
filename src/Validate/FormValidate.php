<?php

namespace KxAdmin\Validate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormValidate extends FormRequest
{
    public function failedValidation($validator)
    {
        $error = $validator->errors();
        throw new HttpResponseException(response()->json([
            'data' => [
                'field' =>$error->keys()[0],
                'message' => $error->first()
            ],
            'message'   => $error->first(),
            'code'    => 422
        ], 422));
    }
}
