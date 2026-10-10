<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSampleRequestRequest;
use App\Models\Product;
use App\Models\SampleRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SampleRequestController extends Controller
{
    public function __invoke(StoreSampleRequestRequest $request, Product $product): RedirectResponse
    {
        SampleRequest::create([
            ...$request->validated(),
            'product_id' => $product->id,
            'product_name' => $product->seo_title,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Sample request received. Our export team will contact you soon.',
        ]);

        return back();
    }
}
