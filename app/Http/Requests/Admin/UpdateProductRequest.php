<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('products', 'slug')->ignore($this->route('product')),
            ],
            'title_part1' => ['required', 'string', 'max:255'],
            'title_part1_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title_part2' => ['required', 'string', 'max:255'],
            'title_part2_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'subtitle_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'net_piece_weight' => ['nullable', 'string', 'max:255'],
            'pieces_per_pack' => ['nullable', 'string', 'max:255'],
            'shelf_life' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],
            'allergens' => ['nullable', 'string', 'max:255'],
            'moq' => ['nullable', 'string', 'max:255'],
            'spec_image' => ['nullable', 'image:allow_svg', 'max:5120'],
            'technical_data_sheet' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,svg', 'max:10240'],
            'pieces_per_pack_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,svg', 'max:10240'],
            'experience_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
