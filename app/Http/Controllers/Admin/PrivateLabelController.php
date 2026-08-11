<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrivateLabelSection;
use App\Http\Controllers\Controller;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;
use Inertia\Inertia;
use Inertia\Response;

class PrivateLabelController extends Controller
{
    public function index(): Response
    {
        $items = PrivateLabelItem::orderBy('order')->get()->groupBy(fn (PrivateLabelItem $item): string => $item->section->value);

        return Inertia::render('admin/PrivateLabel/Index', [
            'sections' => collect(PrivateLabelSection::cases())->map(fn (PrivateLabelSection $section): array => [
                'value' => $section->value,
                'label' => $section->label(),
                'description' => $section->description(),
                'items' => $items->get($section->value, collect())->values(),
            ]),
            'packagingFormats' => PackagingFormat::orderBy('order')->orderBy('id')->get(),
            'unreadInquiries' => PrivateLabelInquiry::whereNull('read_at')->count(),
        ]);
    }
}
