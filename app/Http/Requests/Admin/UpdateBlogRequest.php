<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
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
            'blog_category_id' => ['required', 'integer', Rule::exists('blog_categories', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('blogs', 'slug')->ignore($this->route('blog')),
            ],
            'author' => ['nullable', 'string', 'max:255'],
            'source_link' => ['nullable', 'string', 'max:2048'],
            'custom_date' => ['nullable', 'string', 'max:100'],
            'read_duration' => ['nullable', 'string', 'max:50'],
            'mini_review' => ['nullable', 'string', 'max:1000'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'references' => ['nullable', 'array'],
            'references.*' => ['nullable', 'string', 'max:2048'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:500'],
            'faqs.*.answer' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
