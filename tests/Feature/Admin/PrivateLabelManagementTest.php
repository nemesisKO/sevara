<?php

use App\Enums\PrivateLabelSection;
use App\Enums\UserRole;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access the admin private label page', function () {
    $response = $this->get(route('admin.private-label.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin private label page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.private-label.index'));

    $response->assertForbidden();
});

test('admins can view the private label page grouped by section', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['title' => 'Export Support']);
    PackagingFormat::factory()->create(['name' => 'Premium Gift Box']);
    PrivateLabelInquiry::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.private-label.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/PrivateLabel/Index')
        ->has('sections', 3)
        ->where('sections.0.value', 'highlight')
        ->has('sections.0.items', 1)
        ->where('sections.0.items.0.title', 'Export Support')
        ->has('packagingFormats', 1)
        ->where('unreadInquiries', 2)
    );
});

test('admins can add an item to a section', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.private-label.items.store'), [
        'section' => PrivateLabelSection::Step->value,
        'icon' => UploadedFile::fake()->image('icon.png'),
        'title' => 'Sample',
        'description' => 'We develop samples and finalize flavors.',
    ]);

    $response->assertRedirect(route('admin.private-label.index'));

    $item = PrivateLabelItem::sole();
    expect($item->section)->toBe(PrivateLabelSection::Step)
        ->and($item->title)->toBe('Sample')
        ->and($item->order)->toBe(0);
    Storage::disk('public')->assertExists($item->icon_path);
});

test('an item may be added without an icon', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.private-label.items.store'), [
        'section' => PrivateLabelSection::Highlight->value,
        'title' => 'Export Support',
        'description' => 'End-to-end logistics assistance.',
    ]);

    $response->assertRedirect(route('admin.private-label.index'));
    expect(PrivateLabelItem::sole()->icon_path)->toBeNull();
});

test('adding an item requires a valid section, title and description', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.private-label.items.store'), [
        'section' => 'nonsense',
    ]);

    $response->assertSessionHasErrors(['section', 'title', 'description']);
});

test('regular users cannot add an item', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.private-label.items.store'), [
        'section' => PrivateLabelSection::Highlight->value,
        'title' => 'Export Support',
        'description' => 'End-to-end logistics assistance.',
    ]);

    $response->assertForbidden();
    expect(PrivateLabelItem::count())->toBe(0);
});

test('admins can delete an item and its icon', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $item = PrivateLabelItem::factory()->create([
        'icon_path' => UploadedFile::fake()->image('icon.png')->store('private-label/icons', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.private-label.items.destroy', $item));

    $response->assertRedirect(route('admin.private-label.index'));
    expect(PrivateLabelItem::find($item->id))->toBeNull();
    Storage::disk('public')->assertMissing($item->icon_path);
});

test('admins can reorder items within a section only', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $first = PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['order' => 0]);
    $second = PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['order' => 1]);
    $otherSection = PrivateLabelItem::factory()->section(PrivateLabelSection::Step)->create(['order' => 0]);

    $this->actingAs($admin)->patch(route('admin.private-label.items.move-up', $second))
        ->assertRedirect(route('admin.private-label.index'));

    expect($second->refresh()->order)->toBe(0)
        ->and($first->refresh()->order)->toBe(1)
        ->and($otherSection->refresh()->order)->toBe(0);

    $this->actingAs($admin)->patch(route('admin.private-label.items.move-down', $second));

    expect($second->refresh()->order)->toBe(1)
        ->and($first->refresh()->order)->toBe(0);
});

test('admins can add a packaging format', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.packaging-formats.store'), [
        'name' => 'Metal Tin / Luxury Can',
        'description' => 'Durable and luxurious tin for long shelf life.',
        'image' => UploadedFile::fake()->image('tin.jpg'),
        'is_featured' => '1',
    ]);

    $response->assertRedirect(route('admin.private-label.index'));

    $format = PackagingFormat::sole();
    expect($format->name)->toBe('Metal Tin / Luxury Can')
        ->and($format->is_featured)->toBeTrue();
    Storage::disk('public')->assertExists($format->image_path);
});

test('a packaging format left off the slider is stored as an other format', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->post(route('admin.packaging-formats.store'), [
        'name' => 'Window Box',
    ]);

    expect(PackagingFormat::sole()->is_featured)->toBeFalse();
});

test('adding a packaging format requires a name', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.packaging-formats.store'), []);

    $response->assertSessionHasErrors('name');
});

test('admins can add a picture and edit the text of an existing packaging format', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create([
        'name' => 'Glass Jar / PET Jar',
        'description' => 'Transparent jar for visibility, trust and impulse purchase.',
        'image_path' => null,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.packaging-formats.update', $format), [
        'name' => 'Glass Jar',
        'description' => 'Transparent jar that shows the product on shelf.',
        'image' => UploadedFile::fake()->image('jar.jpg'),
        'is_featured' => '1',
    ]);

    $response->assertRedirect(route('admin.private-label.index'));

    $format->refresh();
    expect($format->name)->toBe('Glass Jar')
        ->and($format->description)->toBe('Transparent jar that shows the product on shelf.')
        ->and($format->is_featured)->toBeTrue();
    Storage::disk('public')->assertExists($format->image_path);
});

test('updating a packaging format image replaces the old file', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create([
        'image_path' => UploadedFile::fake()->image('old.jpg')->store('packaging-formats', 'public'),
    ]);
    $oldPath = $format->image_path;

    $this->actingAs($admin)->patch(route('admin.packaging-formats.update', $format), [
        'name' => $format->name,
        'image' => UploadedFile::fake()->image('new.jpg'),
        'is_featured' => '1',
    ]);

    expect($format->refresh()->image_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($format->image_path);
});

test('updating a packaging format without a file keeps the current image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create([
        'image_path' => UploadedFile::fake()->image('tin.jpg')->store('packaging-formats', 'public'),
    ]);
    $imagePath = $format->image_path;

    $this->actingAs($admin)->patch(route('admin.packaging-formats.update', $format), [
        'name' => 'Metal Tin',
        'is_featured' => '1',
    ]);

    expect($format->refresh()->image_path)->toBe($imagePath);
    Storage::disk('public')->assertExists($imagePath);
});

test('a slider format can be moved to the other formats list', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create(['name' => 'Window Box']);

    $this->actingAs($admin)->patch(route('admin.packaging-formats.update', $format), [
        'name' => 'Window Box',
    ]);

    expect($format->refresh()->is_featured)->toBeFalse();
});

test('updating a packaging format requires a name', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create();

    $response = $this->actingAs($admin)->patch(route('admin.packaging-formats.update', $format), []);

    $response->assertSessionHasErrors('name');
});

test('admins can delete a packaging format and its image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $format = PackagingFormat::factory()->create([
        'image_path' => UploadedFile::fake()->image('tin.jpg')->store('packaging-formats', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.packaging-formats.destroy', $format));

    $response->assertRedirect(route('admin.private-label.index'));
    expect(PackagingFormat::find($format->id))->toBeNull();
    Storage::disk('public')->assertMissing($format->image_path);
});

test('admins can reorder packaging formats', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $first = PackagingFormat::factory()->create(['order' => 0]);
    $second = PackagingFormat::factory()->create(['order' => 1]);

    $this->actingAs($admin)->patch(route('admin.packaging-formats.move-up', $second));

    expect($second->refresh()->order)->toBe(0)
        ->and($first->refresh()->order)->toBe(1);
});

test('reordering a format never swaps it across the slider and other lists', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $featured = PackagingFormat::factory()->create(['order' => 0]);
    $other = PackagingFormat::factory()->other()->create(['order' => 1]);

    $this->actingAs($admin)->patch(route('admin.packaging-formats.move-up', $other));

    expect($other->refresh()->order)->toBe(1)
        ->and($featured->refresh()->order)->toBe(0);
});

test('admins can read the inquiries sent from the public form', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    PrivateLabelInquiry::factory()->create([
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'source_page' => '/about',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.private-label.inquiries.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/PrivateLabel/Inquiries')
        ->has('inquiries', 1)
        ->where('inquiries.0.full_name', 'Amina Yusuf')
        ->where('inquiries.0.email', 'amina@example.com')
        ->where('inquiries.0.source_page', '/about')
    );
});

test('only admins can generate a stable personal form link for a private label inquiry', function () {
    $inquiry = PrivateLabelInquiry::factory()->create(['intent' => 'private_label']);
    $otherInquiry = PrivateLabelInquiry::factory()->create(['intent' => 'private_label']);
    $linkRoute = route('admin.private-label.inquiries.project-link', $inquiry);

    $this->post($linkRoute)->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->post($linkRoute)->assertForbidden();
    expect($inquiry->refresh()->project_access_token)->toBeNull();

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
        ->post($linkRoute)->assertRedirect(route('admin.private-label.inquiries.index'));

    $token = $inquiry->refresh()->project_access_token;
    expect($token)->toHaveLength(48);

    $this->post($linkRoute)->assertRedirect(route('admin.private-label.inquiries.index'));
    expect($inquiry->refresh()->project_access_token)->toBe($token);

    $this->post(route('admin.private-label.inquiries.project-link', $otherInquiry))
        ->assertRedirect(route('admin.private-label.inquiries.index'));
    expect($otherInquiry->refresh()->project_access_token)->not->toBe($token);

    $this->get(route('admin.private-label.inquiries.index'))
        ->assertInertia(fn ($page) => $page
            ->where('inquiries.0.project_url', route('kaveh', $otherInquiry->project_access_token))
            ->where('inquiries.1.project_url', route('kaveh', $token))
            ->where('inquiries.0.project_exists', false)
        );
});

test('a personal project link cannot be generated for another inquiry type', function () {
    $inquiry = PrivateLabelInquiry::factory()->create(['intent' => 'sample_request']);

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
        ->post(route('admin.private-label.inquiries.project-link', $inquiry))
        ->assertNotFound();

    expect($inquiry->refresh()->project_access_token)->toBeNull();
});

test('regular users cannot read the inquiries', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.private-label.inquiries.index'));

    $response->assertForbidden();
});

test('admins can toggle an inquiry between read and unread', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $inquiry = PrivateLabelInquiry::factory()->create();

    $this->actingAs($admin)->patch(route('admin.private-label.inquiries.read', $inquiry))
        ->assertRedirect(route('admin.private-label.inquiries.index'));

    expect($inquiry->refresh()->read_at)->not->toBeNull();

    $this->actingAs($admin)->patch(route('admin.private-label.inquiries.read', $inquiry));

    expect($inquiry->refresh()->read_at)->toBeNull();
});

test('admins can delete an inquiry', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $inquiry = PrivateLabelInquiry::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.private-label.inquiries.destroy', $inquiry));

    $response->assertRedirect(route('admin.private-label.inquiries.index'));
    expect(PrivateLabelInquiry::count())->toBe(0);
});
