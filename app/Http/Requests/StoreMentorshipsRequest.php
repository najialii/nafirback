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
            'mentorship_id'=>['required'],
            'mentor_id'=>['required'],
            'mentee_id'=>['required'],
            'selecteday'=>['required'],
            'selectedtime'=>['required'],
            'message'=>['required'],
            'status'=>['required'],
        ];
    }
}
