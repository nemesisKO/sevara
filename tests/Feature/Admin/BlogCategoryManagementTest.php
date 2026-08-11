<?php

use App\Enums\UserRole;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;

test('guests cannot access the admin blog categories page', function () {
    $response = $this->get(route('admin.blog-categories.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access the admin blog categories page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.blog-categories.index'));

    $response->assertForbidden();
});

test('admins can view the blog categories page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    BlogCategory::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.blog-categories.index'));

    $response->assertOk();
});

test('admins can add a blog category', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.blog-categories.store'), [
        'name' => 'Export Guide',
    ]);

    $response->assertRedirect(route('admin.blog-categories.index'));
    $category = BlogCategory::sole();
    expect($category->name)->toBe('Export Guide');
    expect($category->slug)->toBe('export-guide');
});

test('adding a blog category requires a name', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->post(route('admin.blog-categories.store'), []);

    $response->assertSessionHasErrors(['name']);
});

test('admins can delete an unused blog category', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $category = BlogCategory::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.blog-categories.destroy', $category));

    $response->assertRedirect(route('admin.blog-categories.index'));
    expect(BlogCategory::find($category->id))->toBeNull();
});

test('admins cannot delete a blog category that still has posts', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $category = BlogCategory::factory()->create();
    Blog::factory()->create(['blog_category_id' => $category->id]);

    $response = $this->actingAs($admin)->delete(route('admin.blog-categories.destroy', $category));

    $response->assertRedirect(route('admin.blog-categories.index'));
    expect(BlogCategory::find($category->id))->not->toBeNull();
});
