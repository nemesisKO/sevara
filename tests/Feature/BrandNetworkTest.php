<?php

use App\Models\Brand;

test('the brand network page displays brands', function () {
    Brand::factory()->count(3)->create();

    $response = $this->get(route('brand-network'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('BrandNetwork')
        ->has('brands', 3)
    );
});

test('the home page displays brands', function () {
    Brand::factory()->count(2)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->has('brands', 2)
    );
});
