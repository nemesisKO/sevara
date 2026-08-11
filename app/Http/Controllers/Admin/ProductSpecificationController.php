<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductSpecificationRequest;
use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductSpecificationController extends Controller
{
    public function store(StoreProductSpecificationRequest $request, Product $product): RedirectResponse
    {
        $product->specifications()->create([
            'icon_path' => $request->file('icon')->store('products/spec-icons', 'public'),
            'question' => $request->validated('question'),
            'answer' => $request->validated('answer'),
            'order' => ($product->specifications()->max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Specification added.']);

        return to_route('admin.products.edit', $product);
    }

    public function destroy(Product $product, ProductSpecification $specification): RedirectResponse
    {
        Storage::disk('public')->delete($specification->icon_path);

        $specification->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Specification deleted.']);

        return to_route('admin.products.edit', $product);
    }

    public function moveUp(Product $product, ProductSpecification $specification): RedirectResponse
    {
        $this->swapOrder($specification, $product->specifications()->where('order', '<', $specification->order)->orderByDesc('order')->first());

        return to_route('admin.products.edit', $product);
    }

    public function moveDown(Product $product, ProductSpecification $specification): RedirectResponse
    {
        $this->swapOrder($specification, $product->specifications()->where('order', '>', $specification->order)->orderBy('order')->first());

        return to_route('admin.products.edit', $product);
    }

    private function swapOrder(ProductSpecification $specification, ?ProductSpecification $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$specificationOrder, $siblingOrder] = [$specification->order, $sibling->order];

        $specification->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $specificationOrder]);
    }
}
