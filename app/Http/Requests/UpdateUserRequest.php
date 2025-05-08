<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'           => ['sometimes', 'required', 'string'],
            'email'          => ['sometimes', 'required', 'email'],
            'phone'          => ['sometimes', 'required', 'string'],
            'skills'         => ['sometimes', 'required', 'string'],
            'exp_years'      => ['sometimes', 'required', 'integer'],
            'country'        => ['sometimes', 'required', 'string'],
            'profile_pic'    => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Optional profile picture
            'department_id'  => ['sometimes', 'required', 'integer'],
            'expertise'      => ['sometimes', 'required', 'string'],
            'education'      => ['sometimes', 'required', 'string'],
            'certificates'   => ['sometimes', 'required', 'string'],
        ];
    }
}