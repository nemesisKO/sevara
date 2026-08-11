<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogBlockRequest;
use App\Http\Requests\Admin\UpdateBlogBlockRequest;
use App\Models\Blog;
use App\Models\BlogBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BlogBlockController extends Controller
{
    public function store(StoreBlogBlockRequest $request, Blog $blog): RedirectResponse
    {
        $type = $request->validated('type');

        $data = [
            'type' => $type,
            'order' => ($blog->blocks()->max('order') ?? -1) + 1,
            'title' => $request->validated('title'),
            'link_text' => $request->validated('link_text'),
            'link_url' => $request->validated('link_url'),
        ];

        if ($type === 'article' || $type === 'hint') {
            $data['body'] = $request->validated('body');
        }

        if ($type === 'image' && $request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('blogs/blocks', 'public');
        }

        if ($type === 'table') {
            $rows = (int) $request->validated('rows');
            $columns = (int) $request->validated('columns');

            $data['table_data'] = array_fill(0, $rows, array_fill(0, $columns, ['text' => '', 'link_url' => '']));
        }

        $blog->blocks()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Block added.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function update(UpdateBlogBlockRequest $request, Blog $blog, BlogBlock $block): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($block->image_path) {
                Storage::disk('public')->delete($block->image_path);
            }

            $data['image_path'] = $request->file('image')->store('blogs/blocks', 'public');
        }

        $block->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Block saved.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function destroy(Blog $blog, BlogBlock $block): RedirectResponse
    {
        if ($block->image_path) {
            Storage::disk('public')->delete($block->image_path);
        }

        $block->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Block deleted.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function moveUp(Blog $blog, BlogBlock $block): RedirectResponse
    {
        $this->swapOrder($block, $blog->blocks()->where('order', '<', $block->order)->orderByDesc('order')->first());

        return to_route('admin.blogs.edit', $blog);
    }

    public function moveDown(Blog $blog, BlogBlock $block): RedirectResponse
    {
        $this->swapOrder($block, $blog->blocks()->where('order', '>', $block->order)->orderBy('order')->first());

        return to_route('admin.blogs.edit', $blog);
    }

    private function swapOrder(BlogBlock $block, ?BlogBlock $sibling): void
    {
        if (! $sibling) {
            return;
        }

        [$blockOrder, $siblingOrder] = [$block->order, $sibling->order];

        $block->update(['order' => $siblingOrder]);
        $sibling->update(['order' => $blockOrder]);
    }
}
