<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
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
                'fname'=> ['required', 'string'],
                'lname'=> ['required', 'string'],
                'email'           => ['required', 'email'],
                'img'             => ['required', 'string'], 
                'phone'           => ['required', 'string'],
                'skills'          => ['required' ,'array'], 
                'exp_years'       => ['required', 'integer'],
                'country'         => ['required', 'string'],
                'expertise'       => ['required', 'array'],
                'expertise.*.name'        => ['required', 'string'],
                'expertise.*.description' => ['nullable', 'string'],
                'expertise.*.start_date'  => ['required', 'date'],
                'expertise.*.end_date'    => ['nullable', 'date'],
        
                'education'       => ['required', 'array'],
                'education.*.name'        => ['required', 'string'],
                'education.*.institution' => ['required', 'string'],
                'education.*.start_date'  => ['required', 'date'],
                'education.*.end_date'    => ['required', 'date'],
        
                'certificates'    => ['required', 'array'],
                'certificates.*.name'       => ['required', 'string'],
                'certificates.*.issued_by'  => ['required', 'string'],
                'certificates.*.issue_date' => ['required', 'date'],        
                'linkden_profile' => ['required', 'url'],
        ];
    }
}
