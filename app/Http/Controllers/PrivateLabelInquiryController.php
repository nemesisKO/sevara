<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrivateLabelInquiryRequest;
use App\Models\PrivateLabelInquiry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class PrivateLabelInquiryController extends Controller
{
    public function store(StorePrivateLabelInquiryRequest $request): RedirectResponse
    {
        PrivateLabelInquiry::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Thank you. Our export team will review your inquiry and contact you.']);

        return back();
    }
}
