<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MentorshipUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'mentor_id' => 'sometimes|required|integer|exists:users,id',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
            'date' => 'nullable|date',
            'days' => 'sometimes|required|array', 
            'available_times' => 'nullable|array', 
        ];
    }
}
