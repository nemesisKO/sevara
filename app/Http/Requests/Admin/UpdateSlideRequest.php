<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'link' => ['required', 'string', 'max:2048'],
            'primary_cta_text' => ['nullable', 'string', 'max:255', 'required_with:primary_cta_link'],
            'primary_cta_link' => ['nullable', 'string', 'max:2048', 'required_with:primary_cta_text'],
            'secondary_cta_text' => ['nullable', 'string', 'max:255', 'required_with:secondary_cta_link'],
            'secondary_cta_link' => ['nullable', 'string', 'max:2048', 'required_with:secondary_cta_text'],
        ];
    }
}
