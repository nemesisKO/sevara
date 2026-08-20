<?php

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access the admin settings page', function () {
    $response = $this->get(route('admin.settings.edit'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin settings page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.settings.edit'));

    $response->assertForbidden();
});

test('admins can view the settings page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.settings.edit'));

    $response->assertOk()->assertInertia(fn ($page) => $page
        ->component('admin/Settings/Index')
        ->missing('packagingFormats')
    );
});

test('admins can upload a website logo', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]);

    $response->assertRedirect(route('admin.settings.edit'));

    $setting = Setting::current();
    expect($setting->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($setting->logo_path);
});

test('replacing the logo deletes the old file', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $oldPath = UploadedFile::fake()->image('old.png')->store('branding', 'public');
    Setting::current()->update(['logo_path' => $oldPath]);

    $this->actingAs($admin)->put(route('admin.settings.update'), [
        'logo' => UploadedFile::fake()->image('new.png'),
    ]);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists(Setting::current()->fresh()->logo_path);
});

test('regular users cannot upload a website logo', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.settings.update'), [
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]);

    $response->assertForbidden();
    expect(Setting::current()->logo_path)->toBeNull();
});

test('the logo must be an image', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'logo' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['logo']);
});

test('admins can upload a catalogue PDF', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'catalogue' => UploadedFile::fake()->create('catalogue.pdf', 500, 'application/pdf'),
    ]);

    $response->assertRedirect(route('admin.settings.edit'));

    $setting = Setting::current();
    expect($setting->catalogue_path)->not->toBeNull();
    Storage::disk('public')->assertExists($setting->catalogue_path);
});

test('replacing the catalogue deletes the old file', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $oldPath = UploadedFile::fake()->create('old-catalogue.pdf', 500, 'application/pdf')->store('catalogues', 'public');
    Setting::current()->update(['catalogue_path' => $oldPath]);

    $this->actingAs($admin)->put(route('admin.settings.update'), [
        'catalogue' => UploadedFile::fake()->create('new-catalogue.pdf', 500, 'application/pdf'),
    ]);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists(Setting::current()->fresh()->catalogue_path);
});

test('the catalogue must be a PDF', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'catalogue' => UploadedFile::fake()->image('catalogue.png'),
    ]);

    $response->assertSessionHasErrors(['catalogue']);
});

test('the logo url is shared with every page', function () {
    Storage::fake('public');
    Setting::current()->update([
        'logo_path' => UploadedFile::fake()->image('logo.png')->store('branding', 'public'),
    ]);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->where('settings.logo_url', Setting::current()->logo_url)
        ->where('settings.catalogue_url', Setting::current()->catalogue_url));
});
