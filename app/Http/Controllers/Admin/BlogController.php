<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Blogs/Index', [
            'blogs' => Blog::with('category')->latest('id')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Blogs/Create', [
            'categories' => BlogCategory::orderBy('name')->get(),
        ]);
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $blog = Blog::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Blog post created.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function edit(Blog $blog): Response
    {
        return Inertia::render('admin/Blogs/Edit', [
            'blog' => $blog->load(['blocks', 'tags']),
            'categories' => BlogCategory::orderBy('name')->get(),
            'allTags' => Tag::orderBy('title')->get(),
        ]);
    }

    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($blog->image_path) {
                Storage::disk('public')->delete($blog->image_path);
            }

            $data['image_path'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Blog post updated.']);

        return to_route('admin.blogs.edit', $blog);
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $paths = $blog->blocks->pluck('image_path')->filter();

        if ($blog->image_path) {
            $paths->push($blog->image_path);
        }

        Storage::disk('public')->delete($paths->all());

        $blog->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Blog post deleted.']);

        return to_route('admin.blogs.index');
    }
}
