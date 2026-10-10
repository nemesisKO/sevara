<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSocialLinksRequest extends FormRequest
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
            'social_links' => ['present', 'array', 'max:12'],
            'social_links.*.label' => ['required', 'string', 'max:50'],
            'social_links.*.icon' => ['required', 'string', Rule::in([
                'instagram',
                'linkedin',
                'facebook',
                'youtube',
                'x',
                'tiktok',
                'whatsapp',
                'website',
            ])],
            'social_links.*.url' => ['required', 'url', 'starts_with:https://,http://', 'max:2048'],
        ];
    }
}
