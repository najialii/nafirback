<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MentorshipReqRequest extends FormRequest
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
            'mentorship_id' => ['required'],
            'mentor_id' => ['required'],
            'mentee_id' => ['required'],
            'sele_date' => ['required'],
            'sele_time' => ['required'],
            'message' => ['required'],
            'status' => ['required']
        ];
    }
}
