<?php

namespace App\Http\Requests;

use App\Base\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CategoryRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        // Determine if it's an update by checking if the route contains an ID.
        $id = $this->route('id');

        return [
            'name' => [
                // If updating, the 'name' is not required, but it must be unique except for the current category.
                $id ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($id) // Ignore the current category's name during update
            ],
            'description' => 'nullable|string',
            //    'image' => [
            //        // If updating, the image is optional, otherwise required for creation.
            //        'nullable',
            //        'mimes:jpeg,png,jpg',
            //        'max:2048', // Max 2MB
            //        //make string or image

            //    ],
            'is_active' => 'boolean',
        ];
    }
}
