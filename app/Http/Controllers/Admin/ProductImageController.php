<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductImageController extends Controller
{
    public function store(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        $product->images()->create([
            'image_path' => $request->file('image')->store('products', 'public'),
            'order' => ($product->images()->max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Image added.']);

        return to_route('admin.products.edit', $product);
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Image deleted.']);

        return to_route('admin.products.edit', $product);
    }

    public function moveUp(Product $product, ProductImage $image): RedirectResponse
    {
        $this->swapOrder($image, $product->images()->where('order', '<', $image->order)->orderByDesc('order')->first());

        return to_route('admin.products.edit', $product);
    }

    public function moveDown(Product $product, ProductImage $image): RedirectResponse
    {
        $this->swapOrder($image, $product->images()->where('order', '>', $image->order)->orderBy('order')->first());

        return to_route('admin.products.edit', $product);
    }

    private function swapOrder(ProductImage $image, ?ProductImage $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$imageOrder, $siblingOrder] = [$image->order, $sibling->order];

        $image->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $imageOrder]);
    }
}
