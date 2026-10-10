<?php

namespace App\Http\Requests;

use App\Models\SampleRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSampleRequestRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'sample_volume' => ['required', Rule::in(SampleRequest::VOLUMES)],
            'shipping_term' => ['required', Rule::in(SampleRequest::SHIPPING_TERMS)],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
