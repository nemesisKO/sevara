<?php

use App\Enums\UserRole;
use App\Models\NewsletterSubscriber;
use App\Models\User;

test('guests cannot access newsletter subscribers', function () {
    $response = $this->get(route('admin.newsletter-subscribers.index'));

    $response->assertRedirect(route('login'));
});

test('regular users cannot access newsletter subscribers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.newsletter-subscribers.index'));

    $response->assertForbidden();
});

test('admins can view newsletter subscribers', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

    $response = $this->actingAs($admin)->get(route('admin.newsletter-subscribers.index'));

    $response->assertOk()->assertInertia(fn ($page) => $page
        ->component('admin/NewsletterSubscribers/Index')
        ->has('subscribers', 1)
        ->where('subscribers.0.email', 'reader@example.com')
    );
});

test('admins can remove a newsletter subscriber', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $subscriber = NewsletterSubscriber::factory()->create();

    $response = $this->actingAs($admin)->delete(
        route('admin.newsletter-subscribers.destroy', $subscriber),
    );

    $response->assertRedirect(route('admin.newsletter-subscribers.index'));
    expect(NewsletterSubscriber::find($subscriber->id))->toBeNull();
});
