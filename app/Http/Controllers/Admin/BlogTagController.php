<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogTagRequest;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BlogTagController extends Controller
{
    public function store(StoreBlogTagRequest $request, Blog $blog): RedirectResponse
    {
        $tagId = $request->validated('tag_id');

        $tag = $tagId
            ? Tag::findOrFail($tagId)
            : Tag::firstOrCreate(['title' => $request->validated('title')]);

        $blog->tags()->syncWithoutDetaching($tag);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tag added.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function destroy(Blog $blog, Tag $tag): RedirectResponse
    {
        $blog->tags()->detach($tag);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tag removed.']);

        return to_route('admin.blogs.edit', $blog);
    }
}
