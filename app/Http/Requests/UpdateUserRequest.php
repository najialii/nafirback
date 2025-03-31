<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // Get the HTTP method for the request (PUT or PATCH)
        $method = $this->method();

        if ($method === 'PUT') {
            return [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['sometimes','required'],
                'phone' => ['required'],
                'skills' => ['required'],
                'exp_years' => ['required', 'integer'],
                'country' => ['required'],
            ];
        } elseif ($method === 'PATCH') {
            return [
                'name' => ['sometimes', 'required'],
                'email' => ['sometimes', 'required', 'email'],
                'password' => ['sometimes', 'required'],
                'phone' => ['sometimes', 'required'],
                'skills' => ['sometimes', 'required'],
                'exp_years' => ['sometimes', 'required', 'integer'],
                'country' => ['sometimes', 'required'],
            ];
        }

        return [];
    }
}
