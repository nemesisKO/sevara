<?php

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access the admin brands page', function () {
    $response = $this->get(route('admin.brands.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin brands page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.brands.index'));

    $response->assertForbidden();
});

test('admins can view the brands page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Brand::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.brands.index'));

    $response->assertOk();
});

test('admins can add a brand', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.brands.store'), [
        'name' => 'Tangesir',
        'image' => UploadedFile::fake()->image('brand.jpg'),
        'link' => 'https://example.com/tangesir',
    ]);

    $response->assertRedirect(route('admin.brands.index'));

    $brand = Brand::sole();
    expect($brand->name)->toBe('Tangesir');
    expect($brand->link)->toBe('https://example.com/tangesir');
    expect($brand->order)->toBe(0);
    Storage::disk('public')->assertExists($brand->image_path);
});

test('new brands are appended after existing ones', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Brand::factory()->create(['order' => 0]);
    Brand::factory()->create(['order' => 1]);

    $this->actingAs($admin)->post(route('admin.brands.store'), [
        'name' => 'New Brand',
        'image' => UploadedFile::fake()->image('brand.jpg'),
        'link' => 'https://example.com/new-brand',
    ]);

    expect(Brand::where('name', 'New Brand')->sole()->order)->toBe(2);
});

test('regular users cannot add a brand', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.brands.store'), [
        'name' => 'Tangesir',
        'image' => UploadedFile::fake()->image('brand.jpg'),
        'link' => 'https://example.com/tangesir',
    ]);

    $response->assertForbidden();
    expect(Brand::count())->toBe(0);
});

test('adding a brand requires a name, an image and a link', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.brands.store'), []);

    $response->assertSessionHasErrors(['name', 'image', 'link']);
});

test('admins can delete a brand', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $brand = Brand::factory()->create([
        'image_path' => UploadedFile::fake()->image('brand.jpg')->store('brands', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.brands.destroy', $brand));

    $response->assertRedirect(route('admin.brands.index'));
    expect(Brand::find($brand->id))->toBeNull();
    Storage::disk('public')->assertMissing($brand->image_path);
});

test('admins can move a brand up, swapping order with the previous sibling', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $first = Brand::factory()->create(['order' => 0]);
    $second = Brand::factory()->create(['order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.brands.move-up', $second));

    $response->assertRedirect(route('admin.brands.index'));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

test('admins can move a brand down, swapping order with the next sibling', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $first = Brand::factory()->create(['order' => 0]);
    $second = Brand::factory()->create(['order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.brands.move-down', $first));

    $response->assertRedirect(route('admin.brands.index'));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

test('moving the first brand up does nothing', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $first = Brand::factory()->create(['order' => 0]);

    $this->actingAs($admin)->patch(route('admin.brands.move-up', $first));

    expect($first->fresh()->order)->toBe(0);
});
