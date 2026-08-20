<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePackagingFormatRequest;
use App\Http\Requests\Admin\UpdatePackagingFormatRequest;
use App\Models\PackagingFormat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PackagingFormatController extends Controller
{
    public function store(StorePackagingFormatRequest $request): RedirectResponse
    {
        PackagingFormat::create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'image_path' => $request->file('image')?->store('packaging-formats', 'public'),
            'is_featured' => $request->validated('is_featured'),
            'order' => (PackagingFormat::max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Packaging format added.']);

        return $this->redirectAfterChange($request);
    }

    public function update(UpdatePackagingFormatRequest $request, PackagingFormat $packagingFormat): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($packagingFormat->image_path) {
                Storage::disk('public')->delete($packagingFormat->image_path);
            }

            $data['image_path'] = $request->file('image')->store('packaging-formats', 'public');
        }

        $packagingFormat->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Packaging format saved.']);

        return $this->redirectAfterChange($request);
    }

    public function destroy(Request $request, PackagingFormat $packagingFormat): RedirectResponse
    {
        if ($packagingFormat->image_path) {
            Storage::disk('public')->delete($packagingFormat->image_path);
        }

        $packagingFormat->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Packaging format deleted.']);

        return $this->redirectAfterChange($request);
    }

    public function moveUp(Request $request, PackagingFormat $packagingFormat): RedirectResponse
    {
        $this->swapOrder($packagingFormat, PackagingFormat::where('is_featured', $packagingFormat->is_featured)
            ->where('order', '<', $packagingFormat->order)
            ->orderByDesc('order')
            ->first());

        return $this->redirectAfterChange($request);
    }

    public function moveDown(Request $request, PackagingFormat $packagingFormat): RedirectResponse
    {
        $this->swapOrder($packagingFormat, PackagingFormat::where('is_featured', $packagingFormat->is_featured)
            ->where('order', '>', $packagingFormat->order)
            ->orderBy('order')
            ->first());

        return $this->redirectAfterChange($request);
    }

    private function swapOrder(PackagingFormat $format, ?PackagingFormat $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$formatOrder, $siblingOrder] = [$format->order, $sibling->order];

        $format->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $formatOrder]);
    }

    private function redirectAfterChange(Request $request): RedirectResponse
    {
        return to_route($request->query('return_to') === 'settings'
            ? 'admin.settings.edit'
            : 'admin.private-label.index');
    }
}
