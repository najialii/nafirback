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
            'email' => ['required', 'email'],
            'password' => ['required'],
            // 'role' => ['required', Rule::in(['mentor', 'admin', 'mentee'])],
            'department_id' => ['required'],
            'phone' => ['required'],
            'skills' => ['required'],
            'exp_years' => ['required', 'integer'], // Assuming it's an integer
            'country' => ['required'],

        ];
    }
    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //     ]);
    // }
}
