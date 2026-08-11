<?php

use App\Enums\UserRole;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access the admin slides page', function () {
    $response = $this->get(route('admin.slides.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin slides page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.slides.index'));

    $response->assertForbidden();
});

test('admins can view the slides page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Slide::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.slides.index'));

    $response->assertOk();
});

test('admins can add a slide', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.slides.store'), [
        'image' => UploadedFile::fake()->image('slide.jpg'),
        'link' => 'https://example.com',
    ]);

    $response->assertRedirect(route('admin.slides.index'));

    $slide = Slide::sole();
    expect($slide->link)->toBe('https://example.com');
    Storage::disk('public')->assertExists($slide->image_path);
});

test('regular users cannot add a slide', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.slides.store'), [
        'image' => UploadedFile::fake()->image('slide.jpg'),
        'link' => 'https://example.com',
    ]);

    $response->assertForbidden();
    expect(Slide::count())->toBe(0);
});

test('adding a slide requires an image and a link', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.slides.store'), []);

    $response->assertSessionHasErrors(['image', 'link']);
});

test('admins can delete a slide', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $slide = Slide::factory()->create([
        'image_path' => UploadedFile::fake()->image('slide.jpg')->store('slides', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.slides.destroy', $slide));

    $response->assertRedirect(route('admin.slides.index'));
    expect(Slide::find($slide->id))->toBeNull();
    Storage::disk('public')->assertMissing($slide->image_path);
});
