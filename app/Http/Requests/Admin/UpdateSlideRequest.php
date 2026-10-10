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
            'image' => ['nullable', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:5120'],
            'link' => ['required', 'string', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'title_style' => ['nullable', 'array:top,left,right,bottom,size,weight,color'],
            'description_style' => ['nullable', 'array:top,left,right,bottom,size,weight,color'],
            'title_style.top' => ['nullable', 'numeric', 'between:0,100'],
            'title_style.left' => ['nullable', 'numeric', 'between:0,100'],
            'title_style.right' => ['nullable', 'numeric', 'between:0,100'],
            'title_style.bottom' => ['nullable', 'numeric', 'between:0,100'],
            'title_style.size' => ['nullable', 'integer', 'between:12,120'],
            'title_style.weight' => ['nullable', 'integer', 'in:400,500,600,700,800,900'],
            'title_style.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description_style.top' => ['nullable', 'numeric', 'between:0,100'],
            'description_style.left' => ['nullable', 'numeric', 'between:0,100'],
            'description_style.right' => ['nullable', 'numeric', 'between:0,100'],
            'description_style.bottom' => ['nullable', 'numeric', 'between:0,100'],
            'description_style.size' => ['nullable', 'integer', 'between:12,120'],
            'description_style.weight' => ['nullable', 'integer', 'in:400,500,600,700,800,900'],
            'description_style.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_cta_text' => ['nullable', 'string', 'max:255', 'required_with:primary_cta_link'],
            'primary_cta_link' => ['nullable', 'string', 'max:2048', 'required_with:primary_cta_text'],
            'secondary_cta_text' => ['nullable', 'string', 'max:255', 'required_with:secondary_cta_link'],
            'secondary_cta_link' => ['nullable', 'string', 'max:2048', 'required_with:secondary_cta_text'],
        ];
    }
}
