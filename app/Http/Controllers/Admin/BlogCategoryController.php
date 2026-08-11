<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/BlogCategories/Index', [
            'categories' => BlogCategory::withCount('blogs')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        $name = $request->validated('name');

        BlogCategory::create([
            'name' => $name,
            'slug' => (string) str($name)->slug(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category added.']);

        return to_route('admin.blog-categories.index');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        if ($category->blogs()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Move or delete its blog posts before deleting this category.']);

            return to_route('admin.blog-categories.index');
        }

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category deleted.']);

        return to_route('admin.blog-categories.index');
    }
}
