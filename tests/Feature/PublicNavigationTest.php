<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;

test('the public navigation targets all resolve', function (string $routeName) {
    $response = $this->get(route($routeName));

    $response->assertOk();
})->with(['products.index', 'private-label', 'about', 'contact', 'catalog.request']);

test('public pages share the products navigation dropdown data', function () {
    $product = Product::factory()->create([
        'title_part1' => 'Dark',
        'title_part2' => 'Chocolate Date',
    ]);

    $response = $this->get(route('about'));

    $response->assertInertia(fn ($page) => $page->has('navProducts', 1)
        ->where('navProducts.0.title', 'Dark Chocolate Date')
        ->where('navProducts.0.href', route('products.show', $product)));
});

test('admin pages do not load the products navigation dropdown data', function () {
    Product::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.slides.index'));

    $response->assertInertia(fn ($page) => $page->where('navProducts', []));
});
