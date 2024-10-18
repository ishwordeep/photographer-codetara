<?php

namespace App\Base;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            apiResponse([
                'status' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY
            ])
        );
    }
}
