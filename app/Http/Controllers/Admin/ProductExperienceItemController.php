<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductExperienceItemRequest;
use App\Models\Product;
use App\Models\ProductExperienceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductExperienceItemController extends Controller
{
    public function store(StoreProductExperienceItemRequest $request, Product $product): RedirectResponse
    {
        $product->experienceItems()->create([
            'icon_path' => $request->file('icon')->store('products/experience-icons', 'public'),
            'title' => $request->validated('title'),
            'order' => ($product->experienceItems()->max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Experience item added.']);

        return to_route('admin.products.edit', $product);
    }

    public function destroy(Product $product, ProductExperienceItem $experienceItem): RedirectResponse
    {
        Storage::disk('public')->delete($experienceItem->icon_path);

        $experienceItem->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Experience item deleted.']);

        return to_route('admin.products.edit', $product);
    }

    public function moveUp(Product $product, ProductExperienceItem $experienceItem): RedirectResponse
    {
        $this->swapOrder($experienceItem, $product->experienceItems()->where('order', '<', $experienceItem->order)->orderByDesc('order')->first());

        return to_route('admin.products.edit', $product);
    }

    public function moveDown(Product $product, ProductExperienceItem $experienceItem): RedirectResponse
    {
        $this->swapOrder($experienceItem, $product->experienceItems()->where('order', '>', $experienceItem->order)->orderBy('order')->first());

        return to_route('admin.products.edit', $product);
    }

    private function swapOrder(ProductExperienceItem $item, ?ProductExperienceItem $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$itemOrder, $siblingOrder] = [$item->order, $sibling->order];

        $item->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $itemOrder]);
    }
}
