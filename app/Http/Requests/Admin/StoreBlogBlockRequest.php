<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogBlockRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:article,image,hint,table'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['required_if:type,image', 'nullable', 'image', 'max:5120'],
            'link_text' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'rows' => ['required_if:type,table', 'nullable', 'integer', 'min:1', 'max:12'],
            'columns' => ['required_if:type,table', 'nullable', 'integer', 'min:1', 'max:8'],
        ];
    }
}
