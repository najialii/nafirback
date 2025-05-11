<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name'                 => ['sometimes', 'required', 'string'],
            'email'                => ['sometimes', 'required', 'email'],
            'profile_pic'          => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], 
            'phone'                => ['sometimes', 'required', 'string'],
            'title'                => ['sometimes', 'required', 'string'], 
            'skills'               => ['sometimes', 'required', 'array'], 
            'skills.*'             => ['string'], 
            'education'            => ['sometimes', 'required', 'array'], 
            'education.*.university' => ['required', 'string'],
            'education.*.certificate' => ['required', 'string'],
            'education.*.degree'   => ['required', 'string'],
            'education.*.period'   => ['required', 'array'],
            'education.*.period.start' => ['required', 'string'],
            'education.*.period.end'   => ['required', 'string'],
            'education.*.description'  => ['nullable', 'string'],
            'experience'           => ['sometimes', 'required', 'array'], 
            'experience.*.title'   => ['required', 'string'],
            'experience.*.company' => ['required', 'string'],
            'experience.*.period'  => ['required', 'array'],
            'experience.*.period.start' => ['required', 'string'],
            'experience.*.period.end'   => ['required', 'string'],
            'experience.*.description'  => ['nullable', 'string'],
            'location'             => ['sometimes', 'required', 'array'], 
            'location.country'     => ['required', 'string'],
            'location.city'        => ['required', 'string'],
            'cv_file'              => ['sometimes', 'file', 'mimes:pdf', 'max:2048'], 
            'targeted_locations'   => ['sometimes', 'required', 'array'], 
            'targeted_locations.*' => ['string'], 
            'targeted_industries'  => ['sometimes', 'required', 'array'], 
            'targeted_industries.*' => ['string'], 
            'targeted_titles'      => ['sometimes', 'required', 'array'], 
            'targeted_titles.*'    => ['string'], 
            'career_tasks'         => ['sometimes', 'required', 'string', 'max:2000'], 
            'completion_percentage' => ['sometimes', 'required', 'integer', 'min:0', 'max:100'], 
        ];
    }
}