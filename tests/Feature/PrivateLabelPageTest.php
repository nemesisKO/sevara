<?php

use App\Enums\PrivateLabelSection;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;

test('the private label page shows the managed content', function () {
    PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['title' => 'Product Development']);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Step)->create(['title' => 'Brief']);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Feature)->create(['title' => 'Retail-Ready Packaging']);
    PackagingFormat::factory()->create(['name' => '250g Retail Pouch']);
    PackagingFormat::factory()->other()->create(['name' => 'Window Box']);

    $response = $this->get(route('private-label'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('PrivateLabel')
        ->has('highlights', 1)
        ->where('highlights.0.title', 'Product Development')
        ->has('steps', 1)
        ->where('steps.0.title', 'Brief')
        ->has('features', 1)
        ->where('features.0.title', 'Retail-Ready Packaging')
        ->has('featuredFormats', 1)
        ->where('featuredFormats.0.name', '250g Retail Pouch')
        ->has('otherFormats', 1)
        ->where('otherFormats.0.name', 'Window Box')
        ->has('markets')
    );
});

test('the private label page orders items and formats by their order', function () {
    PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['title' => 'Second', 'order' => 1]);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['title' => 'First', 'order' => 0]);
    PackagingFormat::factory()->create(['name' => 'Later', 'order' => 5]);
    PackagingFormat::factory()->create(['name' => 'Sooner', 'order' => 2]);

    $response = $this->get(route('private-label'));

    $response->assertInertia(fn ($page) => $page
        ->where('highlights.0.title', 'First')
        ->where('highlights.1.title', 'Second')
        ->where('featuredFormats.0.name', 'Sooner')
        ->where('featuredFormats.1.name', 'Later')
    );
});

test('a visitor can send a private label inquiry', function () {
    $response = $this->from(route('private-label'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'company_name' => 'Gulf Retail Group',
        'country' => 'Saudi Arabia',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
        'message' => 'We would like samples before committing.',
    ]);

    $response->assertRedirect(route('private-label'));

    $inquiry = PrivateLabelInquiry::sole();
    expect($inquiry->full_name)->toBe('Amina Yusuf')
        ->and($inquiry->company_name)->toBe('Gulf Retail Group')
        ->and($inquiry->country)->toBe('Saudi Arabia')
        ->and($inquiry->estimated_quantity)->toBe('10,000 units')
        ->and($inquiry->read_at)->toBeNull();
});

test('an inquiry may be sent without a message', function () {
    $response = $this->from(route('private-label'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'company_name' => 'Gulf Retail Group',
        'country' => 'Qatar',
        'product_format' => 'Not sure yet',
        'estimated_quantity' => '5,000 units',
    ]);

    $response->assertRedirect(route('private-label'));
    expect(PrivateLabelInquiry::sole()->message)->toBeNull();
});

test('an inquiry requires the contact and project details', function () {
    $response = $this->post(route('private-label.inquiries.store'), []);

    $response->assertSessionHasErrors([
        'full_name',
        'company_name',
        'country',
        'product_format',
        'estimated_quantity',
    ]);
    expect(PrivateLabelInquiry::count())->toBe(0);
});

test('an inquiry rejects a country outside the supported markets', function () {
    $response = $this->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'company_name' => 'Gulf Retail Group',
        'country' => 'Atlantis',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
    ]);

    $response->assertSessionHasErrors('country');
    expect(PrivateLabelInquiry::count())->toBe(0);
});
