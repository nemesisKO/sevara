<?php

use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;

test('the contact page renders with the inquiry form options', function () {
    PackagingFormat::factory()->create(['name' => '250g Retail Pouch', 'order' => 2]);
    PackagingFormat::factory()->other()->create(['name' => 'Window Box', 'order' => 5]);

    $response = $this->get(route('contact'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Contact')
        ->has('formats', 2)
        ->where('formats.0.name', '250g Retail Pouch')
        ->where('formats.1.name', 'Window Box')
        ->has('markets')
    );
});

test('a visitor can send an inquiry from the contact page', function () {
    $response = $this->from(route('contact'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'company_name' => 'Gulf Retail Group',
        'country' => 'Qatar',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
        'message' => 'We would like samples before committing.',
    ]);

    $response->assertRedirect(route('contact'));

    $inquiry = PrivateLabelInquiry::sole();
    expect($inquiry->full_name)->toBe('Amina Yusuf')
        ->and($inquiry->country)->toBe('Qatar')
        ->and($inquiry->read_at)->toBeNull();
});

test('an invalid inquiry from the contact page returns to the contact page', function () {
    $response = $this->from(route('contact'))->post(route('private-label.inquiries.store'), []);

    $response->assertRedirect(route('contact'));
    $response->assertSessionHasErrors(['full_name', 'company_name', 'country']);
    expect(PrivateLabelInquiry::count())->toBe(0);
});
