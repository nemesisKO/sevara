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
        'mobile_image' => UploadedFile::fake()->image('slide-mobile.jpg'),
        'link' => 'https://example.com',
        'title' => 'Fresh ideas',
        'description' => 'Discover our products',
        'title_style' => ['top' => 20, 'left' => 8, 'size' => 48, 'weight' => 700, 'color' => '#ffffff'],
        'description_style' => ['bottom' => 25, 'right' => 10, 'size' => 20, 'weight' => 400, 'color' => '#f5e5c0'],
        'primary_cta_text' => 'Explore products',
        'primary_cta_link' => '/products',
        'secondary_cta_text' => 'Contact export team',
        'secondary_cta_link' => '/contact',
    ]);

    $response->assertRedirect(route('admin.slides.index'));

    $slide = Slide::sole();
    expect($slide->link)->toBe('https://example.com')
        ->and($slide->primary_cta_text)->toBe('Explore products')
        ->and($slide->primary_cta_link)->toBe('/products')
        ->and($slide->secondary_cta_text)->toBe('Contact export team')
        ->and($slide->secondary_cta_link)->toBe('/contact')
        ->and($slide->title)->toBe('Fresh ideas')
        ->and($slide->description)->toBe('Discover our products')
        ->and($slide->title_style['top'])->toBe(20)
        ->and($slide->description_style['bottom'])->toBe(25)
        ->and($slide->mobile_image_url)->toBe('/storage/'.$slide->mobile_image_path);
    Storage::disk('public')->assertExists($slide->image_path);
    Storage::disk('public')->assertExists($slide->mobile_image_path);
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

test('button text and links must be provided together', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.slides.store'), [
        'image' => UploadedFile::fake()->image('slide.jpg'),
        'link' => 'https://example.com',
        'primary_cta_text' => 'Explore products',
    ]);

    $response->assertSessionHasErrors('primary_cta_link');
});

test('slide appearance values are validated', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.slides.store'), [
        'image' => UploadedFile::fake()->image('slide.jpg'),
        'mobile_image' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
        'link' => '/products',
        'title_style' => ['left' => 101, 'size' => 200, 'weight' => 350, 'color' => 'red'],
    ]);

    $response->assertSessionHasErrors([
        'mobile_image',
        'title_style.left',
        'title_style.size',
        'title_style.weight',
        'title_style.color',
    ]);
});

test('admins can edit a slide call to action buttons', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $slide = Slide::factory()->create();

    $response = $this->actingAs($admin)->patch(route('admin.slides.update', $slide), [
        'link' => '/private-label',
        'primary_cta_text' => 'Explore products',
        'primary_cta_link' => '/products',
        'secondary_cta_text' => 'Contact export team',
        'secondary_cta_link' => '/contact',
    ]);

    $response->assertRedirect(route('admin.slides.index'));

    expect($slide->fresh())
        ->link->toBe('/private-label')
        ->primary_cta_text->toBe('Explore products')
        ->primary_cta_link->toBe('/products')
        ->secondary_cta_text->toBe('Contact export team')
        ->secondary_cta_link->toBe('/contact');
});

test('admins can replace slide images and edit text placement', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $slide = Slide::factory()->create([
        'image_path' => UploadedFile::fake()->image('old-desktop.jpg')->store('slides', 'public'),
        'mobile_image_path' => UploadedFile::fake()->image('old-mobile.jpg')->store('slides', 'public'),
    ]);
    $oldImagePath = $slide->image_path;
    $oldMobileImagePath = $slide->mobile_image_path;

    $response = $this->actingAs($admin)->patch(route('admin.slides.update', $slide), [
        'image' => UploadedFile::fake()->image('new-desktop.jpg'),
        'mobile_image' => UploadedFile::fake()->image('new-mobile.jpg'),
        'link' => $slide->link,
        'title' => 'Updated headline',
        'description' => 'Updated description',
        'title_style' => ['bottom' => 18, 'right' => 12, 'size' => 52, 'weight' => 800, 'color' => '#123456'],
        'description_style' => ['top' => 70, 'left' => 12, 'size' => 22, 'weight' => 500, 'color' => '#abcdef'],
    ]);

    $response->assertRedirect(route('admin.slides.index'));
    $slide->refresh();
    expect($slide->title)->toBe('Updated headline')
        ->and($slide->description)->toBe('Updated description')
        ->and($slide->title_style['right'])->toBe(12)
        ->and($slide->description_style['color'])->toBe('#abcdef');
    Storage::disk('public')->assertExists($slide->image_path);
    Storage::disk('public')->assertExists($slide->mobile_image_path);
    Storage::disk('public')->assertMissing($oldImagePath);
    Storage::disk('public')->assertMissing($oldMobileImagePath);
});

test('admins can delete a slide', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $slide = Slide::factory()->create([
        'image_path' => UploadedFile::fake()->image('slide.jpg')->store('slides', 'public'),
        'mobile_image_path' => UploadedFile::fake()->image('slide-mobile.jpg')->store('slides', 'public'),
    ]);
    $mobileImagePath = $slide->mobile_image_path;

    $response = $this->actingAs($admin)->delete(route('admin.slides.destroy', $slide));

    $response->assertRedirect(route('admin.slides.index'));
    expect(Slide::find($slide->id))->toBeNull();
    Storage::disk('public')->assertMissing($slide->image_path);
    Storage::disk('public')->assertMissing($mobileImagePath);
});
