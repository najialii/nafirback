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
'name'                   => ['sometimes', 'string'],
        'email'                  => ['sometimes', 'email'],
        'profile_pic'            => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'phone'                  => ['sometimes', 'string'],
        'title'                  => ['sometimes', 'string'],
        'skills'                 => ['sometimes', 'array'],
        'skills.*'               => ['string'],
        'education'              => ['sometimes', 'array'],
        'education.*.university' => ['sometimes', 'string'],
        'education.*.certificate'=> ['sometimes', 'string'],
        'education.*.degree'     => ['sometimes', 'string'],
        'education.*.period'     => ['sometimes', 'array'],
        'education.*.period.start' => ['sometimes', 'string'],
        'education.*.period.end'   => ['sometimes', 'string'],
        'education.*.description'  => ['nullable', 'string'],
        'experience'             => ['sometimes', 'array'],
        'experience.*.title'     => ['sometimes', 'string'],
        'experience.*.company'   => ['sometimes', 'string'],
        'experience.*.period'    => ['sometimes', 'array'],
        'experience.*.period.start' => ['sometimes', 'string'],
        'experience.*.period.end'   => ['sometimes', 'string'],
        'experience.*.description'  => ['nullable', 'string'],
        'location'               => ['sometimes', 'array'],
        'location.country'       => ['sometimes', 'string'],
        'location.city'          => ['sometimes', 'string'],
        'cv_file'                => ['sometimes', 'file', 'mimes:pdf', 'max:2048'],
        'targeted_locations'     => ['sometimes', 'array'],
        'targeted_locations.*'   => ['string'],
        'targeted_industries'    => ['sometimes', 'array'],
        'targeted_industries.*'  => ['string'],
        'targeted_titles'        => ['sometimes', 'array'],
        'targeted_titles.*'      => ['string'],
        'career_tasks'           => ['sometimes', 'string', 'max:2000'],
        'completion_percentage'  => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}