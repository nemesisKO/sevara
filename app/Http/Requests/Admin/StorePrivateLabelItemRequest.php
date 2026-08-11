<?php

namespace App\Http\Requests\Admin;

use App\Enums\PrivateLabelSection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrivateLabelItemRequest extends FormRequest
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
            'section' => ['required', Rule::enum(PrivateLabelSection::class)],
            'icon' => ['nullable', 'image:allow_svg', 'max:2048'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
        ];
    }
}
