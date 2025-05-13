<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMentorshipsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'img' => ['nullable', 'string', 'url'],
            'description' => ['required', 'string'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'benefits' => ['nullable', 'string'],
            'session_date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'link' => ['nullable', 'string', 'url'],
         
        ];
    }
}