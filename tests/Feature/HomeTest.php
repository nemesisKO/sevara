<?php

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Slide;

test('the home page displays slides', function () {
    $slide = Slide::factory()->create(['title' => 'A fresh headline']);
    Slide::factory()->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->has('slides', 2)
        ->where('slides.0.title', 'A fresh headline')
        ->where('slides.0.mobile_image_url', '/storage/'.$slide->image_path)
    );
});

test('the home page displays the product collection with a cover image', function () {
    $product = Product::factory()->create(['title_part1' => 'Strawberry']);
    ProductImage::factory()->count(3)->create(['product_id' => $product->id]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('products', 1)
        ->where('products.0.title_part1', 'Strawberry')
        ->has('products.0.images', 1)
    );
});

test('the product collection is ordered by name', function () {
    Product::factory()->create(['title_part1' => 'Milky']);
    Product::factory()->create(['title_part1' => 'Coffee']);

    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
        ->where('products.0.title_part1', 'Coffee')
        ->where('products.1.title_part1', 'Milky')
    );
});
