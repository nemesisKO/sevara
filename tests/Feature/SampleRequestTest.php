<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\User;

test('a visitor can request samples for the product they are viewing', function () {
    $product = Product::factory()->create([
        'title_part1' => 'Dark',
        'title_part2' => 'Chocolate Dates',
    ]);

    $response = $this->from(route('products.show', $product))
        ->post(route('products.sample-requests.store', $product), [
            'full_name' => 'Amina Yusuf',
            'email' => 'amina@example.com',
            'company_name' => 'Yusuf Foods',
            'phone' => '+971 50 123 4567',
            'sample_volume' => '5–10 kg',
            'shipping_term' => 'CIF',
            'description' => 'Please quote delivery to Dubai.',
            'product_id' => 999,
            'product_name' => 'A different product',
        ]);

    $response->assertRedirect(route('products.show', $product));

    expect(SampleRequest::sole())
        ->product_id->toBe($product->id)
        ->product_name->toBe('Dark Chocolate Dates')
        ->full_name->toBe('Amina Yusuf')
        ->email->toBe('amina@example.com')
        ->sample_volume->toBe('5–10 kg')
        ->shipping_term->toBe('CIF')
        ->description->toBe('Please quote delivery to Dubai.')
        ->read_at->toBeNull();
});

test('sample requests require a valid volume and shipping term', function () {
    $product = Product::factory()->create();

    $response = $this->from(route('products.show', $product))
        ->post(route('products.sample-requests.store', $product), [
            'full_name' => 'Amina Yusuf',
            'email' => 'amina@example.com',
            'sample_volume' => '1 tonne',
            'shipping_term' => 'EXW',
        ]);

    $response->assertRedirect(route('products.show', $product));
    $response->assertSessionHasErrors(['sample_volume', 'shipping_term']);
    expect(SampleRequest::count())->toBe(0);
});

test('sample requests need contact details', function () {
    $product = Product::factory()->create();

    $response = $this->post(route('products.sample-requests.store', $product), [
        'sample_volume' => '10–25 kg',
        'shipping_term' => 'FOB',
    ]);

    $response->assertSessionHasErrors(['full_name', 'email']);
    expect(SampleRequest::count())->toBe(0);
});

test('a saved sample request keeps the product name after the product is deleted', function () {
    $product = Product::factory()->create([
        'title_part1' => 'Strawberry',
        'title_part2' => 'Chocolate Dates',
    ]);

    $this->post(route('products.sample-requests.store', $product), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'sample_volume' => '50+ kg',
        'shipping_term' => 'FOB',
    ])->assertRedirect();

    $product->delete();

    expect(SampleRequest::sole())
        ->product_id->toBeNull()
        ->product_name->toBe('Strawberry Chocolate Dates');
});

test('only admins can view sample requests', function () {
    $this->get(route('admin.sample-requests.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.sample-requests.index'))
        ->assertForbidden();
});

test('admins can review sample requests and their rfq details', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    SampleRequest::factory()->create([
        'product_name' => 'Dark Chocolate Dates',
        'sample_volume' => '25–50 kg',
        'shipping_term' => 'FOB',
        'description' => 'Please send pricing for retail samples.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sample-requests.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/SampleRequests/Index')
            ->has('sampleRequests', 1)
            ->where('sampleRequests.0.product_name', 'Dark Chocolate Dates')
            ->where('sampleRequests.0.sample_volume', '25–50 kg')
            ->where('sampleRequests.0.shipping_term', 'FOB')
            ->where('sampleRequests.0.description', 'Please send pricing for retail samples.')
        );
});

test('admins can mark sample requests as read and delete them', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $request = SampleRequest::factory()->create();

    $this->actingAs($admin)
        ->patch(route('admin.sample-requests.read', $request))
        ->assertRedirect(route('admin.sample-requests.index'));
    expect($request->refresh()->read_at)->not->toBeNull();

    $this->actingAs($admin)
        ->patch(route('admin.sample-requests.read', $request))
        ->assertRedirect(route('admin.sample-requests.index'));
    expect($request->refresh()->read_at)->toBeNull();

    $this->actingAs($admin)
        ->delete(route('admin.sample-requests.destroy', $request))
        ->assertRedirect(route('admin.sample-requests.index'));
    expect(SampleRequest::count())->toBe(0);
});
