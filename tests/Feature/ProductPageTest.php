<?php

use App\Models\Product;

test('the products page lists every product', function () {
    $second = Product::factory()->create(['title_part1' => 'Strawberry']);
    $first = Product::factory()->create(['title_part1' => 'Dark']);
    $first->images()->create(['image_path' => 'products/a.jpg', 'order' => 0]);
    $first->images()->create(['image_path' => 'products/b.jpg', 'order' => 1]);

    $response = $this->get(route('products.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Products')
        ->has('products', 2)
        ->where('products.0.title_part1', 'Dark')
        ->where('products.0.slug', $first->slug)
        ->has('products.0.images', 1)
        ->where('products.1.title_part1', 'Strawberry')
        ->where('products.1.slug', $second->slug)
    );
});

test('the products page renders without any products', function () {
    $response = $this->get(route('products.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Products')->has('products', 0));
});

test('the product page displays the product', function () {
    $product = Product::factory()->create([
        'title_part1' => 'Strawberry',
        'title_part2' => 'Chocolate Date',
        'slug' => 'strawberry-chocolate-date',
    ]);
    $product->images()->create(['image_path' => 'products/a.jpg', 'order' => 0]);
    $product->specifications()->create([
        'icon_path' => 'products/spec-icons/a.png',
        'question' => 'Ingredients',
        'answer' => 'Real ingredients. Naturally delicious.',
        'order' => 0,
    ]);
    $product->experienceItems()->create([
        'icon_path' => 'products/experience-icons/a.png',
        'title' => 'Deep Cocoa',
        'description' => 'Bold, rich cocoa for a full-bodied flavor.',
        'order' => 0,
    ]);

    $response = $this->get(route('products.show', $product));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Product')
        ->where('product.title_part1', 'Strawberry')
        ->where('product.title_part2', 'Chocolate Date')
        ->has('product.images', 1)
        ->has('product.specifications', 1)
        ->where('product.specifications.0.question', 'Ingredients')
        ->has('product.experience_items', 1)
        ->where('product.experience_items.0.title', 'Deep Cocoa')
    );
});

test('an unknown product slug 404s', function () {
    $response = $this->get('/products/does-not-exist');

    $response->assertNotFound();
});
