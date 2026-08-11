<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogBlockRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'link_text' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'table_data' => ['nullable', 'array'],
            'table_data.*' => ['array'],
            'table_data.*.*.text' => ['nullable', 'string'],
            'table_data.*.*.link_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
