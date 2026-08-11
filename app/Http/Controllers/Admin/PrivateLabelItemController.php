<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePrivateLabelItemRequest;
use App\Models\PrivateLabelItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PrivateLabelItemController extends Controller
{
    public function store(StorePrivateLabelItemRequest $request): RedirectResponse
    {
        $section = $request->validated('section');

        PrivateLabelItem::create([
            'section' => $section,
            'icon_path' => $request->file('icon')?->store('private-label/icons', 'public'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'order' => (PrivateLabelItem::where('section', $section)->max('order') ?? -1) + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Item added.']);

        return to_route('admin.private-label.index');
    }

    public function destroy(PrivateLabelItem $item): RedirectResponse
    {
        if ($item->icon_path) {
            Storage::disk('public')->delete($item->icon_path);
        }

        $item->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Item deleted.']);

        return to_route('admin.private-label.index');
    }

    public function moveUp(PrivateLabelItem $item): RedirectResponse
    {
        $this->swapOrder($item, $this->siblings($item)->where('order', '<', $item->order)->orderByDesc('order')->first());

        return to_route('admin.private-label.index');
    }

    public function moveDown(PrivateLabelItem $item): RedirectResponse
    {
        $this->swapOrder($item, $this->siblings($item)->where('order', '>', $item->order)->orderBy('order')->first());

        return to_route('admin.private-label.index');
    }

    /**
     * @return Builder<PrivateLabelItem>
     */
    private function siblings(PrivateLabelItem $item): Builder
    {
        return PrivateLabelItem::where('section', $item->section);
    }

    private function swapOrder(PrivateLabelItem $item, ?PrivateLabelItem $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$itemOrder, $siblingOrder] = [$item->order, $sibling->order];

        $item->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $itemOrder]);
    }
}
