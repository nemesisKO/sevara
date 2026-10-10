<?php

use App\Http\Controllers\Admin\BlogBlockController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PackagingFormatController;
use App\Http\Controllers\Admin\PrivateLabelController;
use App\Http\Controllers\Admin\PrivateLabelInquiryController;
use App\Http\Controllers\Admin\PrivateLabelItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductExperienceItemController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductSpecificationController;
use App\Http\Controllers\Admin\SampleRequestController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('settings/social-links', [SettingController::class, 'updateSocialLinks'])->name('settings.social-links.update');

    Route::resource('slides', SlideController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('brands', BrandController::class)->only(['index', 'store', 'destroy']);
    Route::patch('brands/{brand}/move-up', [BrandController::class, 'moveUp'])->name('brands.move-up');
    Route::patch('brands/{brand}/move-down', [BrandController::class, 'moveDown'])->name('brands.move-down');

    Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('sample-requests', [SampleRequestController::class, 'index'])->name('sample-requests.index');
    Route::patch('sample-requests/{sampleRequest}/read', [SampleRequestController::class, 'markAsRead'])->name('sample-requests.read');
    Route::delete('sample-requests/{sampleRequest}', [SampleRequestController::class, 'destroy'])->name('sample-requests.destroy');

    Route::post('products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
    Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
    Route::patch('products/{product}/images/{image}/move-up', [ProductImageController::class, 'moveUp'])->name('products.images.move-up');
    Route::patch('products/{product}/images/{image}/move-down', [ProductImageController::class, 'moveDown'])->name('products.images.move-down');

    Route::post('products/{product}/specifications', [ProductSpecificationController::class, 'store'])->name('products.specifications.store');
    Route::delete('products/{product}/specifications/{specification}', [ProductSpecificationController::class, 'destroy'])->name('products.specifications.destroy');
    Route::patch('products/{product}/specifications/{specification}/move-up', [ProductSpecificationController::class, 'moveUp'])->name('products.specifications.move-up');
    Route::patch('products/{product}/specifications/{specification}/move-down', [ProductSpecificationController::class, 'moveDown'])->name('products.specifications.move-down');

    Route::post('products/{product}/experience-items', [ProductExperienceItemController::class, 'store'])->name('products.experience-items.store');
    Route::delete('products/{product}/experience-items/{experienceItem}', [ProductExperienceItemController::class, 'destroy'])->name('products.experience-items.destroy');
    Route::patch('products/{product}/experience-items/{experienceItem}/move-up', [ProductExperienceItemController::class, 'moveUp'])->name('products.experience-items.move-up');
    Route::patch('products/{product}/experience-items/{experienceItem}/move-down', [ProductExperienceItemController::class, 'moveDown'])->name('products.experience-items.move-down');

    Route::resource('blog-categories', BlogCategoryController::class)
        ->parameters(['blog-categories' => 'category'])
        ->only(['index', 'store', 'destroy']);

    Route::resource('blogs', BlogController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])
        ->name('newsletter-subscribers.index');
    Route::delete('newsletter-subscribers/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])
        ->name('newsletter-subscribers.destroy');

    Route::post('blogs/{blog}/blocks', [BlogBlockController::class, 'store'])->name('blogs.blocks.store');
    Route::patch('blogs/{blog}/blocks/{block}', [BlogBlockController::class, 'update'])->name('blogs.blocks.update');
    Route::delete('blogs/{blog}/blocks/{block}', [BlogBlockController::class, 'destroy'])->name('blogs.blocks.destroy');
    Route::patch('blogs/{blog}/blocks/{block}/move-up', [BlogBlockController::class, 'moveUp'])->name('blogs.blocks.move-up');
    Route::patch('blogs/{blog}/blocks/{block}/move-down', [BlogBlockController::class, 'moveDown'])->name('blogs.blocks.move-down');

    Route::post('blogs/{blog}/tags', [BlogTagController::class, 'store'])->name('blogs.tags.store');
    Route::delete('blogs/{blog}/tags/{tag}', [BlogTagController::class, 'destroy'])->name('blogs.tags.destroy');

    Route::get('private-label', [PrivateLabelController::class, 'index'])->name('private-label.index');

    Route::post('private-label/items', [PrivateLabelItemController::class, 'store'])->name('private-label.items.store');
    Route::delete('private-label/items/{item}', [PrivateLabelItemController::class, 'destroy'])->name('private-label.items.destroy');
    Route::patch('private-label/items/{item}/move-up', [PrivateLabelItemController::class, 'moveUp'])->name('private-label.items.move-up');
    Route::patch('private-label/items/{item}/move-down', [PrivateLabelItemController::class, 'moveDown'])->name('private-label.items.move-down');

    Route::post('packaging-formats', [PackagingFormatController::class, 'store'])->name('packaging-formats.store');
    Route::patch('packaging-formats/{packagingFormat}', [PackagingFormatController::class, 'update'])->name('packaging-formats.update');
    Route::delete('packaging-formats/{packagingFormat}', [PackagingFormatController::class, 'destroy'])->name('packaging-formats.destroy');
    Route::patch('packaging-formats/{packagingFormat}/move-up', [PackagingFormatController::class, 'moveUp'])->name('packaging-formats.move-up');
    Route::patch('packaging-formats/{packagingFormat}/move-down', [PackagingFormatController::class, 'moveDown'])->name('packaging-formats.move-down');

    Route::get('private-label/inquiries', [PrivateLabelInquiryController::class, 'index'])->name('private-label.inquiries.index');
    Route::patch('private-label/inquiries/{inquiry}/read', [PrivateLabelInquiryController::class, 'markAsRead'])->name('private-label.inquiries.read');
    Route::delete('private-label/inquiries/{inquiry}', [PrivateLabelInquiryController::class, 'destroy'])->name('private-label.inquiries.destroy');
});
