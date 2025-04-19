<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityUpdateRequest extends FormRequest
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
            'description' => 'nullable|string',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'eventsSchedule' => 'nullable|array',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|max:100',
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'benifites' => 'nullable|string',

        ];
    }
}
