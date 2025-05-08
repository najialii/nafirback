<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            //
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required'],
            // 'department_id' => ['required'],
            // 'phone' => ['required'],
            // 'skills' => ['nullable'],
            // 'exp_years' => ['required', 'integer'],
            // 'country' => ['required'],
            // 'isActive' => ['required', 'boolean'],

        ];
    }
    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //     ]);
    // }
}
