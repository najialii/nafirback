<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
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
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'author_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'content' => 'required|string',
            'slug' => 'required|string|unique:blogs,slug',
            'featured' => 'nullable|boolean',
        ];
    }
}
