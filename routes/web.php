<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandNetworkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivateLabelController;
use App\Http\Controllers\PrivateLabelInquiryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('brand-network', [BrandNetworkController::class, 'index'])->name('brand-network');
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('private-label', [PrivateLabelController::class, 'index'])->name('private-label');
Route::post('private-label/inquiries', [PrivateLabelInquiryController::class, 'store'])->name('private-label.inquiries.store');

Route::inertia('about', 'About')->name('about');

Route::get('contact', [ContactController::class, 'index'])->name('contact');

Route::inertia('request-catalog', 'ComingSoon', ['title' => 'Request Catalog'])->name('catalog.request');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
