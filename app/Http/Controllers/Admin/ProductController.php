<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Products/Index', [
            'products' => Product::with('images')->orderBy('title_part1')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Products/Create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($this->productData($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Product created.']);

        return to_route('admin.products.edit', $product);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('admin/Products/Edit', [
            'product' => $product->load(['images', 'specifications', 'experienceItems']),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        if ($request->hasFile('spec_image') && $product->spec_image_path) {
            Storage::disk('public')->delete($product->spec_image_path);
        }

        if ($request->hasFile('technical_data_sheet') && $product->technical_data_sheet_path) {
            Storage::disk('public')->delete($product->technical_data_sheet_path);
        }

        if ($request->hasFile('pieces_per_pack_file') && $product->pieces_per_pack_path) {
            Storage::disk('public')->delete($product->pieces_per_pack_path);
        }

        $product->update($this->productData($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Product updated.']);

        return to_route('admin.products.edit', $product);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $paths = $product->images->pluck('image_path')
            ->merge($product->specifications->pluck('icon_path'))
            ->merge($product->experienceItems->pluck('icon_path'));

        if ($product->spec_image_path) {
            $paths->push($product->spec_image_path);
        }

        if ($product->technical_data_sheet_path) {
            $paths->push($product->technical_data_sheet_path);
        }

        if ($product->pieces_per_pack_path) {
            $paths->push($product->pieces_per_pack_path);
        }

        Storage::disk('public')->delete($paths->all());

        $product->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Product deleted.']);

        return to_route('admin.products.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(StoreProductRequest|UpdateProductRequest $request): array
    {
        $data = $request->safe()->except([
            'spec_image',
            'technical_data_sheet',
            'pieces_per_pack_file',
        ]);

        if ($request->hasFile('spec_image')) {
            $data['spec_image_path'] = $request->file('spec_image')->store('products/spec', 'public');
        }

        if ($request->hasFile('technical_data_sheet')) {
            $data['technical_data_sheet_path'] = $request->file('technical_data_sheet')->store('products/technical-files', 'public');
        }

        if ($request->hasFile('pieces_per_pack_file')) {
            $data['pieces_per_pack_path'] = $request->file('pieces_per_pack_file')->store('products/technical-files', 'public');
        }

        return $data;
    }
}
