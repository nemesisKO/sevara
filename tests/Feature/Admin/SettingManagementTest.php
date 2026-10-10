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
    Setting::current()->update(['social_links' => [
        ['label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://instagram.com/sevara'],
    ]]);

    $response = $this->actingAs($admin)->get(route('admin.settings.edit'));

    $response->assertOk()->assertInertia(fn ($page) => $page
        ->component('admin/Settings/Index')
        ->where('setting.social_links.0.icon', 'instagram')
        ->missing('packagingFormats')
    );
});

test('admins can add edit reorder and remove footer social links', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $instagram = ['label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://instagram.com/sevara'];
    $linkedin = ['label' => 'LinkedIn', 'icon' => 'linkedin', 'url' => 'https://linkedin.com/company/sevara'];

    $this->actingAs($admin)->put(route('admin.settings.social-links.update'), [
        'social_links' => [$instagram, $linkedin],
    ])->assertRedirect(route('admin.settings.edit'));

    expect(Setting::current()->fresh()->social_links)->toBe([$instagram, $linkedin]);

    $this->actingAs($admin)->put(route('admin.settings.social-links.update'), [
        'social_links' => [
            ['label' => 'Our YouTube', 'icon' => 'youtube', 'url' => 'https://youtube.com/@sevara'],
            $instagram,
        ],
    ])->assertRedirect(route('admin.settings.edit'));

    expect(Setting::current()->fresh()->social_links)->toBe([
        ['label' => 'Our YouTube', 'icon' => 'youtube', 'url' => 'https://youtube.com/@sevara'],
        $instagram,
    ]);

    $this->actingAs($admin)->put(route('admin.settings.social-links.update'), [
        'social_links' => [],
    ])->assertRedirect(route('admin.settings.edit'));

    expect(Setting::current()->fresh()->social_links)->toBe([]);
});

test('social links require a supported icon and an http or https URL', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->put(route('admin.settings.social-links.update'), [
        'social_links' => [
            ['label' => 'Unsafe', 'icon' => 'custom-svg', 'url' => 'javascript:alert(1)'],
        ],
    ])->assertSessionHasErrors(['social_links.0.icon', 'social_links.0.url']);

    expect(Setting::current()->fresh()->social_links)->toBeNull();
});

test('regular users cannot update footer social links', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('admin.settings.social-links.update'), [
        'social_links' => [
            ['label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://instagram.com/sevara'],
        ],
    ])->assertForbidden();

    expect(Setting::current()->fresh()->social_links)->toBeNull();
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
        'social_links' => [
            ['label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://instagram.com/sevara'],
        ],
    ]);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->where('settings.logo_url', Setting::current()->logo_url)
        ->where('settings.catalogue_url', Setting::current()->catalogue_url)
        ->where('settings.social_links.0.url', 'https://instagram.com/sevara'));
});
