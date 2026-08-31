<?php

namespace App\Http\Controllers;

use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact', [
            'formats' => PackagingFormat::orderBy('order')->orderBy('id')->get(),
            'markets' => PrivateLabelInquiry::MARKETS,
            'products' => Product::query()
                ->orderBy('title_part1')
                ->orderBy('title_part2')
                ->get(['id', 'title_part1', 'title_part2'])
                ->map(fn (Product $product): array => ['id' => $product->id, 'name' => $product->seo_title])
                ->values(),
        ]);
    }
}
