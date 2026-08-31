<?php

namespace App\Http\Controllers;

use App\Enums\PrivateLabelSection;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PrivateLabelController extends Controller
{
    public function index(): Response
    {
        $items = PrivateLabelItem::orderBy('order')->get()->groupBy(fn (PrivateLabelItem $item): string => $item->section->value);
        $formats = PackagingFormat::orderBy('order')->orderBy('id')->get();

        return Inertia::render('PrivateLabel', [
            'highlights' => $items->get(PrivateLabelSection::Highlight->value, collect())->values(),
            'steps' => $items->get(PrivateLabelSection::Step->value, collect())->values(),
            'features' => $items->get(PrivateLabelSection::Feature->value, collect())->values(),
            'featuredFormats' => $formats->where('is_featured', true)->values(),
            'otherFormats' => $formats->where('is_featured', false)->values(),
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
