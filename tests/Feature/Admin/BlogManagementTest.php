<?php

use App\Enums\UserRole;
use App\Mail\BlogPublished;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\NewsletterSubscriber;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

function validBlogPayload(array $overrides = []): array
{
    return array_merge([
        'blog_category_id' => BlogCategory::factory()->create()->id,
        'title' => 'The Future of Chocolate Dates in Global Retail',
        'slug' => 'future-of-chocolate-dates-in-global-retail',
        'author' => 'Sevara Editorial Team',
        'source_link' => 'https://example.com/source',
        'custom_date' => 'Autumn 2026',
        'read_duration' => '7',
        'mini_review' => 'Why premium date confectionery is becoming a must-have category.',
        'meta_description' => 'A look at the growth of premium date confectionery.',
    ], $overrides);
}

test('guests cannot access the admin blog page', function () {
    $response = $this->get(route('admin.blogs.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin blog page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.blogs.index'));

    $response->assertForbidden();
});

test('admins can view the blog list page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Blog::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.blogs.index'));

    $response->assertOk();
});

test('admins can create a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.blogs.store'), validBlogPayload());

    $blog = Blog::sole();
    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($blog->title)->toBe('The Future of Chocolate Dates in Global Retail');
    expect($blog->slug)->toBe('future-of-chocolate-dates-in-global-retail');
});

test('creating a blog announces it to newsletter subscribers in one bulk email', function () {
    Storage::fake('public');
    Mail::fake();
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    NewsletterSubscriber::factory()->create(['email' => 'first@example.com']);
    NewsletterSubscriber::factory()->create(['email' => 'second@example.com']);

    $response = $this->actingAs($admin)->post(
        route('admin.blogs.store'),
        validBlogPayload(['image' => UploadedFile::fake()->image('cover.jpg')]),
    );

    $blog = Blog::sole();
    $response->assertRedirect(route('admin.blogs.edit', $blog));

    Mail::assertSent(BlogPublished::class, function (BlogPublished $mail) use ($blog): bool {
        return $mail->blog->is($blog)
            && $mail->hasBcc('first@example.com')
            && $mail->hasBcc('second@example.com');
    });
});

test('regular users cannot create a blog post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.blogs.store'), validBlogPayload());

    $response->assertForbidden();
    expect(Blog::count())->toBe(0);
});

test('creating a blog post requires the core fields', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.blogs.store'), []);

    $response->assertSessionHasErrors(['blog_category_id', 'title', 'slug']);
});

test('blog slugs must be unique', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Blog::factory()->create(['slug' => 'future-of-chocolate-dates-in-global-retail']);

    $response = $this->actingAs($admin)->post(route('admin.blogs.store'), validBlogPayload());

    $response->assertSessionHasErrors(['slug']);
});

test('admins can view the edit page for a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.blogs.edit', $blog));

    $response->assertOk();
});

test('admins can update a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.blogs.update', $blog),
        validBlogPayload([
            'blog_category_id' => $blog->blog_category_id,
            'slug' => $blog->slug,
            'title' => 'Updated Title',
        ]),
    );

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($blog->fresh()->title)->toBe('Updated Title');
});

test('admins can upload a cover image for a blog post', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.blogs.update', $blog),
        validBlogPayload([
            'blog_category_id' => $blog->blog_category_id,
            'slug' => $blog->slug,
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ]),
    );

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();
    expect($blog->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($blog->image_path);
});

test('admins can save references and faqs on a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->put(
        route('admin.blogs.update', $blog),
        validBlogPayload([
            'blog_category_id' => $blog->blog_category_id,
            'slug' => $blog->slug,
            'references' => ['https://example.com/a', 'https://example.com/b'],
            'faqs' => [
                ['question' => 'Is this halal?', 'answer' => 'Yes, all products are halal certified.'],
            ],
        ]),
    );

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();
    expect($blog->references)->toBe(['https://example.com/a', 'https://example.com/b']);
    expect($blog->faqs)->toBe([
        ['question' => 'Is this halal?', 'answer' => 'Yes, all products are halal certified.'],
    ]);
});

test('admins can delete a blog post and its images', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create([
        'image_path' => UploadedFile::fake()->image('cover.jpg')->store('blogs', 'public'),
    ]);
    $block = $blog->blocks()->create([
        'type' => 'image',
        'order' => 0,
        'image_path' => UploadedFile::fake()->image('block.jpg')->store('blogs/blocks', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.blogs.destroy', $blog));

    $response->assertRedirect(route('admin.blogs.index'));
    expect(Blog::find($blog->id))->toBeNull();
    Storage::disk('public')->assertMissing($blog->image_path);
    Storage::disk('public')->assertMissing($block->image_path);
});

// Content blocks

test('admins can add a text block to a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.blocks.store', $blog), [
        'type' => 'article',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $block = $blog->blocks()->sole();
    expect($block->type)->toBe('article');
    expect($block->order)->toBe(0);
});

test('admins can add an image block to a blog post', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.blocks.store', $blog), [
        'type' => 'image',
        'image' => UploadedFile::fake()->image('block.jpg'),
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $block = $blog->blocks()->sole();
    expect($block->type)->toBe('image');
    Storage::disk('public')->assertExists($block->image_path);
});

test('adding an image block requires an image', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.blocks.store', $blog), [
        'type' => 'image',
    ]);

    $response->assertSessionHasErrors(['image']);
});

test('admins can add a table block with the requested grid size', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.blocks.store', $blog), [
        'type' => 'table',
        'rows' => 2,
        'columns' => 3,
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $block = $blog->blocks()->sole();
    expect($block->table_data)->toHaveCount(2);
    expect($block->table_data[0])->toHaveCount(3);
});

test('admins can update a text block body', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $block = $blog->blocks()->create(['type' => 'article', 'order' => 0]);

    $response = $this->actingAs($admin)->patch(route('admin.blogs.blocks.update', [$blog, $block]), [
        'body' => '<p>Updated content</p>',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($block->fresh()->body)->toBe('<p>Updated content</p>');
});

test('admins can update a table block grid', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $block = $blog->blocks()->create([
        'type' => 'table',
        'order' => 0,
        'table_data' => [[['text' => '', 'link_url' => '']]],
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.blogs.blocks.update', [$blog, $block]), [
        'title' => 'Comparison',
        'table_data' => [
            [['text' => 'Feature', 'link_url' => ''], ['text' => 'Sevara', 'link_url' => '']],
        ],
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $block->refresh();
    expect($block->title)->toBe('Comparison');
    expect($block->table_data[0][0]['text'])->toBe('Feature');
});

test('admins can delete a content block', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $block = $blog->blocks()->create([
        'type' => 'image',
        'order' => 0,
        'image_path' => UploadedFile::fake()->image('block.jpg')->store('blogs/blocks', 'public'),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.blogs.blocks.destroy', [$blog, $block]));

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($blog->blocks()->count())->toBe(0);
    Storage::disk('public')->assertMissing($block->image_path);
});

test('admins can reorder content blocks', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $first = $blog->blocks()->create(['type' => 'article', 'order' => 0]);
    $second = $blog->blocks()->create(['type' => 'article', 'order' => 1]);

    $response = $this->actingAs($admin)->patch(route('admin.blogs.blocks.move-up', [$blog, $second]));

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($first->fresh()->order)->toBe(1);
    expect($second->fresh()->order)->toBe(0);
});

// Tags

test('admins can create and attach a new tag', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.tags.store', $blog), [
        'title' => 'Export Guide',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $tag = Tag::sole();
    expect($tag->title)->toBe('Export Guide');
    expect($blog->tags()->where('tags.id', $tag->id)->exists())->toBeTrue();
});

test('admins can attach an existing tag', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $tag = Tag::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.blogs.tags.store', $blog), [
        'tag_id' => $tag->id,
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($blog->tags()->count())->toBe(1);
    expect(Tag::count())->toBe(1);
});

test('admins can remove a tag from a blog post', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $blog = Blog::factory()->create();
    $tag = Tag::factory()->create();
    $blog->tags()->attach($tag);

    $response = $this->actingAs($admin)->delete(route('admin.blogs.tags.destroy', [$blog, $tag]));

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    expect($blog->tags()->count())->toBe(0);
    expect(Tag::find($tag->id))->not->toBeNull();
});
