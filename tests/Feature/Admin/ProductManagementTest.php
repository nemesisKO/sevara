<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validProductPayload(array $overrides = []): array
{
    return array_merge([
        'title_part1' => 'Strawberry',
        'title_part1_color' => '#a83b52',
        'title_part2' => 'Chocolate Date',
        'title_part2_color' => '#0f1c3f',
        'slug' => 'strawberry-chocolate-date',
        'subtitle' => 'Sweet Strawberry. Naturally Delightful.',
        'subtitle_color' => '#a83b52',
        'description' => 'Premium date with almond, coated in smooth strawberry chocolate.',
    ], $overrides);
}

test('guests cannot access the admin products page', function () {
    $response = $this->get(route('admin.products.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin products page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.products.index'));

    $response->assertForbidden();
});

test('admins can view the products page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Product::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.products.index'));

    $response->assertOk();
});

test('admins can create a product', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), validProductPayload());

    $product = Product::sole();
    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->title_part1)->toBe('Strawberry');
    expect($product->title_part1_color)->toBe('#a83b52');
    expect($product->title_part2)->toBe('Chocolate Date');
    expect($product->slug)->toBe('strawberry-chocolate-date');
    expect($product->seo_title)->toBe('Strawberry Chocolate Date');
});

test('regular users cannot create a product', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.products.store'), validProductPayload());

    $response->assertForbidden();
    expect(Product::count())->toBe(0);
});

test('creating a product requires the core fields', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), []);

    $response->assertSessionHasErrors([
        'title_part1', 'title_part1_color', 'title_part2', 'title_part2_color', 'slug', 'subtitle_color',
    ]);
});

test('product slugs must be unique', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Product::factory()->create(['slug' => 'strawberry-chocolate-date']);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), validProductPayload());

    $response->assertSessionHasErrors(['slug']);
});

test('admins can update a product', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.products.update', $product),
        validProductPayload(['title_part1' => 'Mango', 'slug' => $product->slug]),
    );

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->fresh()->title_part1)->toBe('Mango');
});

test('admins can set technical details and upload product files', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.products.update', $product),
        validProductPayload([
            'slug' => $product->slug,
            'net_piece_weight' => '25 g',
            'pieces_per_pack' => '12 pieces',
            'shelf_life' => '12 months',
            'storage' => 'Cool, dry place',
            'allergens' => 'Contains almonds',
            'moq' => '500 cartons',
            'technical_data_sheet' => UploadedFile::fake()->create('technical-data.pdf', 500, 'application/pdf'),
            'pieces_per_pack_file' => UploadedFile::fake()->image('pieces-per-pack.png'),
        ]),
    );

    $response->assertRedirect(route('admin.products.edit', $product));
    $product->refresh();

    expect($product->net_piece_weight)->toBe('25 g');
    expect($product->pieces_per_pack)->toBe('12 pieces');
    expect($product->shelf_life)->toBe('12 months');
    expect($product->storage)->toBe('Cool, dry place');
    expect($product->allergens)->toBe('Contains almonds');
    expect($product->moq)->toBe('500 cartons');
    expect($product->technical_data_sheet_url)->not->toBeNull();
    expect($product->pieces_per_pack_url)->not->toBeNull();
    Storage::disk('public')->assertExists($product->technical_data_sheet_path);
    Storage::disk('public')->assertExists($product->pieces_per_pack_path);
});

test('admins can delete a product and its gallery images', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $image = $product->images()->create([
        'image_path' => UploadedFile::fake()->image('product.jpg')->store('products', 'public'),
        'order' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

    $response->assertRedirect(route('admin.products.index'));
    expect(Product::find($product->id))->toBeNull();
    Storage::disk('public')->assertMissing($image->image_path);
});

test('admins can add an image to a product gallery', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.images.store', $product), [
        'image' => UploadedFile::fake()->image('product.jpg'),
    ]);

    $response->assertRedirect(route('admin.products.edit', $product));
    $image = $product->images()->sole();
    Storage::disk('public')->assertExists($image->image_path);
});

test('admins can delete a gallery image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $image = $product->images()->create([
        'image_path' => UploadedFile::fake()->image('product.jpg')->store('products', 'public'),
        'order' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.products.images.destroy', [$product, $image]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->images()->count())->toBe(0);
    Storage::disk('public')->assertMissing($image->image_path);
});

test('admins can reorder gallery images', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $first = $product->images()->create(['image_path' => 'products/a.jpg', 'order' => 0]);
    $second = $product->images()->create(['image_path' => 'products/b.jpg', 'order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.products.images.move-up', [$product, $second]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

test('admins can upload a specifications image for a product', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.products.update', $product),
        validProductPayload([
            'slug' => $product->slug,
            'spec_image' => UploadedFile::fake()->image('spec.jpg'),
        ]),
    );

    $response->assertRedirect(route('admin.products.edit', $product));
    $product->refresh();
    expect($product->spec_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($product->spec_image_path);
});

test('replacing the specifications image deletes the old file', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('products/spec', 'public');
    $product = Product::factory()->create(['spec_image_path' => $oldPath]);

    $this->actingAs($admin)->put(
        route('admin.products.update', $product),
        validProductPayload([
            'slug' => $product->slug,
            'spec_image' => UploadedFile::fake()->image('new.jpg'),
        ]),
    );

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($product->fresh()->spec_image_path);
});

test('admins can add a specification to a product', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.specifications.store', $product), [
        'icon' => UploadedFile::fake()->image('icon.png'),
        'question' => 'Ingredients',
        'answer' => 'Real ingredients. Naturally delicious.',
    ]);

    $response->assertRedirect(route('admin.products.edit', $product));
    $specification = $product->specifications()->sole();
    expect($specification->question)->toBe('Ingredients');
    expect($specification->answer)->toBe('Real ingredients. Naturally delicious.');
    expect($specification->order)->toBe(0);
    Storage::disk('public')->assertExists($specification->icon_path);
});

test('specification icons can be svg files', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.specifications.store', $product), [
        'icon' => UploadedFile::fake()->createWithContent(
            'icon.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
        ),
        'question' => 'Ingredients',
        'answer' => 'Real ingredients.',
    ]);

    $response->assertRedirect(route('admin.products.edit', $product));
    $specification = $product->specifications()->sole();
    expect($specification->icon_path)->toEndWith('.svg');
    Storage::disk('public')->assertExists($specification->icon_path);
});

test('adding a specification requires an icon, a question and an answer', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.specifications.store', $product), []);

    $response->assertSessionHasErrors(['icon', 'question', 'answer']);
});

test('admins can delete a specification', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $specification = $product->specifications()->create([
        'icon_path' => UploadedFile::fake()->image('icon.png')->store('products/spec-icons', 'public'),
        'question' => 'Ingredients',
        'answer' => 'Real ingredients.',
        'order' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.products.specifications.destroy', [$product, $specification]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->specifications()->count())->toBe(0);
    Storage::disk('public')->assertMissing($specification->icon_path);
});

test('admins can reorder specifications', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $first = $product->specifications()->create(['icon_path' => 'products/spec-icons/a.png', 'question' => 'A', 'answer' => 'A.', 'order' => 0]);
    $second = $product->specifications()->create(['icon_path' => 'products/spec-icons/b.png', 'question' => 'B', 'answer' => 'B.', 'order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.products.specifications.move-up', [$product, $second]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

test('deleting a product removes its specifications image and icons', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $specImagePath = UploadedFile::fake()->image('spec.jpg')->store('products/spec', 'public');
    $product = Product::factory()->create(['spec_image_path' => $specImagePath]);
    $specification = $product->specifications()->create([
        'icon_path' => UploadedFile::fake()->image('icon.png')->store('products/spec-icons', 'public'),
        'question' => 'Ingredients',
        'answer' => 'Real ingredients.',
        'order' => 0,
    ]);

    $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

    expect(Product::find($product->id))->toBeNull();
    Storage::disk('public')->assertMissing($specImagePath);
    Storage::disk('public')->assertMissing($specification->icon_path);
});

test('admins can set the experience section title', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.products.update', $product),
        validProductPayload([
            'slug' => $product->slug,
            'experience_title' => 'The Dark Chocolate Experience',
        ]),
    );

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->fresh()->experience_title)->toBe('The Dark Chocolate Experience');
});

test('admins can add an experience item to a product', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.experience-items.store', $product), [
        'icon' => UploadedFile::fake()->image('icon.png'),
        'title' => 'Deep Cocoa',
    ]);

    $response->assertRedirect(route('admin.products.edit', $product));
    $item = $product->experienceItems()->sole();
    expect($item->title)->toBe('Deep Cocoa');
    expect($item->order)->toBe(0);
    Storage::disk('public')->assertExists($item->icon_path);
});

test('experience item icons can be svg files', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.experience-items.store', $product), [
        'icon' => UploadedFile::fake()->createWithContent(
            'icon.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
        ),
        'title' => 'Deep Cocoa',
    ]);

    $response->assertRedirect(route('admin.products.edit', $product));
    $item = $product->experienceItems()->sole();
    expect($item->icon_path)->toEndWith('.svg');
    Storage::disk('public')->assertExists($item->icon_path);
});

test('adding an experience item requires an icon and a title', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.experience-items.store', $product), []);

    $response->assertSessionHasErrors(['icon', 'title']);
});

test('admins can delete an experience item', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $item = $product->experienceItems()->create([
        'icon_path' => UploadedFile::fake()->image('icon.png')->store('products/experience-icons', 'public'),
        'title' => 'Deep Cocoa',
        'order' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.products.experience-items.destroy', [$product, $item]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->experienceItems()->count())->toBe(0);
    Storage::disk('public')->assertMissing($item->icon_path);
});

test('admins can reorder experience items', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $first = $product->experienceItems()->create(['icon_path' => 'products/experience-icons/a.png', 'title' => 'A', 'order' => 0]);
    $second = $product->experienceItems()->create(['icon_path' => 'products/experience-icons/b.png', 'title' => 'B', 'order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.products.experience-items.move-up', [$product, $second]));

    $response->assertRedirect(route('admin.products.edit', $product));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

test('deleting a product removes its experience item icons', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $product = Product::factory()->create();
    $item = $product->experienceItems()->create([
        'icon_path' => UploadedFile::fake()->image('icon.png')->store('products/experience-icons', 'public'),
        'title' => 'Deep Cocoa',
        'order' => 0,
    ]);

    $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

    expect(Product::find($product->id))->toBeNull();
    Storage::disk('public')->assertMissing($item->icon_path);
});
