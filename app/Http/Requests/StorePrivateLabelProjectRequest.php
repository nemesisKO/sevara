<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrivateLabelProjectRequest extends FormRequest
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
            'job_title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            'company_country' => ['required', 'string', 'max:255'],
            'buyer_type' => ['required', 'array', 'min:1', 'max:8'],
            'buyer_type.*' => ['string', Rule::in(['Importer / Distributor', 'Retailer', 'Private Label Brand', 'E-commerce Brand', 'Retail Chain', 'Corporate Gifting', 'HORECA', 'Wholesaler'])],
            'target_markets' => ['required', 'string', 'max:500'],
            'sales_channel' => ['required', 'array', 'min:1', 'max:7'],
            'sales_channel.*' => ['string', Rule::in(['Supermarkets', 'Gourmet Retail', 'E-commerce', 'Wholesale', 'Corporate Gifting', 'HORECA', 'Duty Free'])],
            'project_type' => ['required', 'string', 'max:255'],
            'positioning' => ['nullable', 'string', 'max:255'],
            'development_type' => ['required', Rule::in(['existing', 'modify', 'custom', 'recommend'])],
            'product_interest' => ['required', 'string', 'max:2000'],
            'chocolate_type' => ['nullable', 'string', 'max:255'],
            'filling' => ['nullable', 'string', 'max:255'],
            'ingredient_requirements' => ['nullable', 'string', 'max:2000'],
            'target_shelf_life' => ['nullable', 'string', 'max:255'],
            'packaging_format' => ['required', 'string', 'max:255'],
            'net_weight' => ['required', 'string', 'max:255'],
            'skus' => ['nullable', 'array', 'max:20'],
            'skus.*.product' => ['nullable', 'string', 'max:255'],
            'skus.*.pack_size' => ['nullable', 'string', 'max:255'],
            'skus.*.quantity' => ['nullable', 'string', 'max:255'],
            'artwork_status' => ['required', 'string', 'max:255'],
            'has_compliance_requirements' => ['required', Rule::in(['yes', 'no', 'unknown'])],
            'certifications' => ['nullable', 'array', 'max:8'],
            'certifications.*' => ['string', 'max:255'],
            'label_languages' => ['nullable', 'string', 'max:255'],
            'technical_requirements' => ['nullable', 'string', 'max:2000'],
            'initial_order' => ['required', 'numeric', 'min:0.1'],
            'order_unit' => ['required', Rule::in(['MT', 'kg', 'cartons'])],
            'forecast' => ['nullable', 'string', 'max:500'],
            'target_price' => ['nullable', 'numeric', 'min:0'],
            'target_currency' => ['nullable', 'string', 'max:20'],
            'delivery_country' => ['required', 'string', 'max:255'],
            'delivery_location' => ['nullable', 'string', 'max:255'],
            'incoterm' => ['nullable', Rule::in(['FOB', 'CIF', 'DAP'])],
            'launch_date' => ['nullable', 'date'],
            'required_delivery_date' => ['nullable', 'date'],
            'sample_request' => ['required', Rule::in(['Yes', 'After reviewing quotation', 'Not yet'])],
            'decision_makers' => ['nullable', 'array', 'max:6'],
            'decision_makers.*' => ['string', 'max:255'],
            'comments' => ['nullable', 'string', 'max:3000'],
            'product_files' => ['nullable', 'array', 'max:10'],
            'product_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp', 'max:25600'],
            'brand_files' => ['nullable', 'array', 'max:10'],
            'brand_files.*' => ['file', 'mimes:pdf,ai,eps,svg,psd,zip,jpg,jpeg,png', 'max:25600'],
            'packaging_files' => ['nullable', 'array', 'max:10'],
            'packaging_files.*' => ['file', 'mimes:pdf,ai,eps,svg,psd,zip,jpg,jpeg,png', 'max:25600'],
            'compliance_files' => ['nullable', 'array', 'max:10'],
            'compliance_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:25600'],
            'accuracy_confirmation' => ['accepted'],
            'privacy_consent' => ['accepted'],
        ];
    }
}
