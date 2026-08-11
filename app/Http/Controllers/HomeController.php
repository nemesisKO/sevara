<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Slide;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'slides' => Slide::orderBy('id')->get(),
            'brands' => Brand::orderBy('order')->get(),
            'products' => Product::with(['images' => fn ($query) => $query->limit(1)])
                ->orderBy('title_part1')
                ->get(),
            'blogs' => Blog::with('category')->latest('id')->take(4)->get(),
        ]);
    }
}
