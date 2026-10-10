<?php

namespace App\Http\Controllers;

use App\Enums\PrivateLabelSection;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrivateLabelController extends Controller
{
    public function formats(): Response
    {
        $formats = PackagingFormat::orderBy('order')->orderBy('id')->get();

        return Inertia::render('PackagingFormats', [
            'featuredFormats' => $formats->where('is_featured', true)->values(),
            'otherFormats' => $formats->where('is_featured', false)->values(),
        ]);
    }

    public function index(Request $request): Response
    {
        $items = PrivateLabelItem::orderBy('order')->get()->groupBy(fn (PrivateLabelItem $item): string => $item->section->value);
        $formats = PackagingFormat::orderBy('order')->orderBy('id')->get();
        $referrer = $request->headers->get('referer');
        $referrerHost = $referrer ? parse_url($referrer, PHP_URL_HOST) : null;
        $referrerPath = $referrer ? parse_url($referrer, PHP_URL_PATH) : null;
        $sourcePage = is_string($referrerHost)
            && strcasecmp($referrerHost, $request->getHost()) === 0
            && is_string($referrerPath)
            && $referrerPath !== $request->getPathInfo()
            && strlen($referrerPath) <= 255
                ? $referrerPath
                : null;

        return Inertia::render('PrivateLabel', [
            'highlights' => $items->get(PrivateLabelSection::Highlight->value, collect())->values(),
            'steps' => $items->get(PrivateLabelSection::Step->value, collect())->values(),
            'features' => $items->get(PrivateLabelSection::Feature->value, collect())->values(),
            'featuredFormats' => $formats->where('is_featured', true)->values(),
            'otherFormats' => $formats->where('is_featured', false)->values(),
            'markets' => PrivateLabelInquiry::MARKETS,
            'initialIntent' => $request->query('intent') === 'general_export_enquiry'
                ? 'general_export_enquiry'
                : null,
            'sourcePage' => $sourcePage,
            'products' => Product::query()
                ->orderBy('title_part1')
                ->orderBy('title_part2')
                ->get(['id', 'title_part1', 'title_part2'])
                ->map(fn (Product $product): array => ['id' => $product->id, 'name' => $product->seo_title])
                ->values(),
        ]);
    }
}
