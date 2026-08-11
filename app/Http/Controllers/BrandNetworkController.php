<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Inertia\Inertia;
use Inertia\Response;

class BrandNetworkController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('BrandNetwork', [
            'brands' => Brand::orderBy('order')->get(),
        ]);
    }
}
