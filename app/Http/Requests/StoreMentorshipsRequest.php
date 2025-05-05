<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => ['required', 'string', 'max:255'],
            'mentor_id' => ['required'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'date' => ['required', 'array'],
            // 'days' => ['required', 'array'],
            // 'available_times' => ['required', 'array'],
            'availability' => ['required']

        ];
    }
}
