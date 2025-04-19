<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|integer|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'eventsSchedule' => 'nullable|array',
            'date' => 'required|date',
            'time' => 'required|string',
            'type' => 'required|string|max:100',
            'user_id' => 'required|integer|exists:users,id',
            'benifites' => 'nullable|string',
        ];
    }
}
