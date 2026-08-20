<?php

use App\Models\NewsletterSubscriber;

test('a visitor can subscribe to the newsletter', function () {
    $response = $this->from('/')->post(route('newsletter.subscribe'), [
        'email' => '  READER@EXAMPLE.COM ',
    ]);

    $response->assertRedirect('/');
    expect(NewsletterSubscriber::sole()->email)->toBe('reader@example.com');
});

test('a duplicate newsletter subscription is stored once', function () {
    NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

    $this->post(route('newsletter.subscribe'), [
        'email' => 'READER@example.com',
    ]);

    expect(NewsletterSubscriber::count())->toBe(1);
});

test('a newsletter subscription requires a valid email address', function () {
    $response = $this->post(route('newsletter.subscribe'), [
        'email' => 'not-an-email',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
    expect(NewsletterSubscriber::count())->toBe(0);
});
