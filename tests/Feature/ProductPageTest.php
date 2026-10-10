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
        'net_piece_weight' => '25 g',
        'pieces_per_pack' => '12 pieces',
        'shelf_life' => '12 months',
        'storage' => 'Cool, dry place',
        'allergens' => 'Contains almonds',
        'moq' => '500 cartons',
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
        'order' => 0,
    ]);
    $otherProduct = Product::factory()->create(['title_part1' => 'Dark']);
    $otherProduct->images()->create([
        'image_path' => 'products/other.jpg',
        'order' => 0,
    ]);

    $response = $this->get(route('products.show', $product));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Product')
        ->where('product.title_part1', 'Strawberry')
        ->where('product.title_part2', 'Chocolate Date')
        ->where('product.net_piece_weight', '25 g')
        ->where('product.pieces_per_pack', '12 pieces')
        ->where('product.shelf_life', '12 months')
        ->where('product.storage', 'Cool, dry place')
        ->where('product.allergens', 'Contains almonds')
        ->where('product.moq', '500 cartons')
        ->where('sampleVolumes.0', '5–10 kg')
        ->where('shippingTerms.0', 'FOB')
        ->where('shippingTerms.1', 'CIF')
        ->has('product.images', 1)
        ->has('product.specifications', 1)
        ->where('product.specifications.0.question', 'Ingredients')
        ->has('product.experience_items', 1)
        ->where('product.experience_items.0.title', 'Deep Cocoa')
        ->has('otherProducts', 1)
        ->where('otherProducts.0.id', $otherProduct->id)
    );
});

test('an unknown product slug 404s', function () {
    $response = $this->get('/products/does-not-exist');

    $response->assertNotFound();
});

test('the product collection falls back to the current product when no others exist', function () {
    $product = Product::factory()->create();

    $response = $this->get(route('products.show', $product));

    $response->assertInertia(fn ($page) => $page
        ->component('Product')
        ->has('otherProducts', 1)
        ->where('otherProducts.0.id', $product->id)
    );
});
