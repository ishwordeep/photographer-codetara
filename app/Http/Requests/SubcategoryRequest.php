<?php

namespace App\Http\Requests;

use App\Base\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => [
                $id ? 'sometimes' : 'required',
                'string',
                'max:100',
                'unique:subcategories,name,' . $id
            ],
            'description' => 'nullable|string',
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
            'is_active' => 'boolean',
            'category_id' => [
                $id ? 'sometimes' : 'required',
                'exists:categories,id'
            ]

        ];
    }
}
