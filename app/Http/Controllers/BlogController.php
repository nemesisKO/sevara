<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Blog/Index', [
            'blogs' => Blog::with('category')->latest()->paginate(9)->withQueryString(),
        ]);
    }

    public function show(Blog $blog): Response
    {
        return Inertia::render('Blog/Show', [
            'blog' => $blog->load(['category', 'blocks', 'tags']),
            'recentBlogs' => Blog::with('category')
                ->where('id', '!=', $blog->id)
                ->latest('id')
                ->take(3)
                ->get(),
        ]);
    }
}
