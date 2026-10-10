<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SampleRequest;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Products', [
            'products' => Product::with(['images' => fn ($query) => $query->limit(1)])
                ->orderBy('title_part1')
                ->get(),
        ]);
    }

    public function show(Product $product): Response
    {
        $otherProducts = Product::with([
            'images' => fn ($query) => $query->limit(1),
        ])
            ->where('id', '!=', $product->id)
            ->orderBy('title_part1')
            ->limit(10)
            ->get();

        return Inertia::render('Product', [
            'product' => $product->load(['images', 'specifications', 'experienceItems']),
            'sampleVolumes' => SampleRequest::VOLUMES,
            'shippingTerms' => SampleRequest::SHIPPING_TERMS,
            'otherProducts' => $otherProducts->isNotEmpty()
                ? $otherProducts
                : Product::with([
                    'images' => fn ($query) => $query->limit(1),
                ])->whereKey($product)->get(),
        ]);
    }
}
