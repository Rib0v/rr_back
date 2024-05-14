<?php

namespace App\Http\Requests\Advert;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:200',
            'price' => 'required|numeric',
            'description' => 'required|string|max:1000',
            'photos' => 'required|array|max:3',
            'photos.*' => 'string|max:255' // в БД установил тип string, он же varchar, поэтому 255
        ];
    }
}
