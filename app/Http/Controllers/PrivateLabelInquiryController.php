<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrivateLabelInquiryRequest;
use App\Models\PrivateLabelInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class PrivateLabelInquiryController extends Controller
{
    public function store(StorePrivateLabelInquiryRequest $request): RedirectResponse
    {
        $inquiry = PrivateLabelInquiry::create($request->validated());

        if (config('services.n8n.auth_header_value')) {
            $response = Http::withHeaders([
                'X-Sevara-Webhook' => 'SDAFHJJHsJKJsdkjgkjkjs@nn',
            ])->post(config('services.n8n.lead_webhook_url'), [
                'submission_id' => $inquiry->id,
                'form_type' => $inquiry->intent,
                'full_name' => $inquiry->full_name,
                'company_name' => $inquiry->company_name,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'product' => $inquiry->product_name,
                'private_label' => null,
                'country' => $inquiry->country,
                'estimated_quantity' => $inquiry->estimated_quantity,
                'message' => $inquiry->message,
            ]);



        } else {
            // dd([
            //     'message' => 'n8n request was not sent because N8N_AUTH_HEADER_VALUE is empty.',
            // ]);
        }

        return back();
    }
}
