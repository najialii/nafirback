<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMentorshipEntryRequest extends FormRequest
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
            'mentorship_id'       => ['required', 'exists:mentorships,id'],
            'start_date'          => ['required', 'date', 'after_or_equal:now'],
            'duration'            => ['required', 'integer', 'min:1'],
            'link'                => ['nullable', 'url'],
            'status'              => ['nullable', 'in:pending,in_progress,completed,cancelled,scheduled'],
            // 'accepted_request_id' => ['nullable', 'exists:mentorship_reqs,id'], 
        ];
    }
}
