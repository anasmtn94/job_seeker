<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyJobRequest extends FormRequest
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
            'resume_choice' => 'required|string',
            'resume_file' => 'required_if:resume_choice,new|file|mimes:pdf|max:5120',
        ];
    }
    public function messages(): array
    {
        return [
            'resume_choice.required' => 'Resume choice is required!',
            'resume_choice.string' => 'Resume choice must be a string!',
            'resume_file.required_if' => 'Resume file is required if resume choice is new!',
            'resume_file.required' => 'Resume file is required!',
            'resume_file.file' => 'Resume file must be a file!',
            'resume_file.mimes' => 'Resume file must be a PDF!',
            'resume_file.max' => 'Resume file must be less than 5MB!',
        ];
    }
}
