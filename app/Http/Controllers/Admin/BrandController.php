<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Brands/Index', [
            'brands' => Brand::orderBy('order')->get(),
        ]);
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        Brand::create([
            'name' => $request->validated('name'),
            'image_path' => $request->file('image')->store('brands', 'public'),
            'link' => $request->validated('link'),
            'order' => (Brand::max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Brand added.']);

        return to_route('admin.brands.index');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        Storage::disk('public')->delete($brand->image_path);

        $brand->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Brand deleted.']);

        return to_route('admin.brands.index');
    }

    public function moveUp(Brand $brand): RedirectResponse
    {
        $this->swapOrder($brand, Brand::where('order', '<', $brand->order)->orderByDesc('order')->first());

        return to_route('admin.brands.index');
    }

    public function moveDown(Brand $brand): RedirectResponse
    {
        $this->swapOrder($brand, Brand::where('order', '>', $brand->order)->orderBy('order')->first());

        return to_route('admin.brands.index');
    }

    private function swapOrder(Brand $brand, ?Brand $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$brandOrder, $siblingOrder] = [$brand->order, $sibling->order];

        $brand->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $brandOrder]);
    }
}
