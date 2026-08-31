<?php

use App\Enums\PrivateLabelSection;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelItem;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::fake();
});

test('the private label page shows the managed content', function () {
    PrivateLabelItem::factory()->section(PrivateLabelSection::Highlight)->create(['title' => 'Product Development']);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Step)->create(['title' => 'Brief']);
    PrivateLabelItem::factory()->section(PrivateLabelSection::Feature)->create(['title' => 'Retail-Ready Packaging']);
    PackagingFormat::factory()->create(['name' => '250g Retail Pouch']);
    PackagingFormat::factory()->other()->create(['name' => 'Window Box']);
    Product::factory()->create([
        'title_part1' => 'Pistachio',
        'title_part2' => 'Chocolate Dates',
    ]);

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
        ->has('products', 1)
        ->where('products.0.name', 'Pistachio Chocolate Dates')
        ->where('markets.0', 'United Arab Emirates')
        ->where('markets.5', 'Bahrain')
        ->where('markets.6', 'Iran')
        ->where('markets.9', 'Pakistan')
    );
});

test('a sample request only requires contact and product details', function () {
    $response = $this->from(route('private-label'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'phone' => '+92 300 123 4567',
        'intent' => 'sample_request',
        'product_name' => 'Pistachio Chocolate Dates',
        'message' => 'Please send a sample box.',
    ]);

    $response->assertRedirect(route('private-label'));
    expect(PrivateLabelInquiry::sole())
        ->intent->toBe('sample_request')
        ->product_name->toBe('Pistachio Chocolate Dates')
        ->country->toBeNull()
        ->product_format->toBeNull()
        ->estimated_quantity->toBeNull();
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
        'email' => 'amina@example.com',
        'phone' => '+966 50 123 4567',
        'intent' => 'private_label',
        'company_name' => 'Yusuf Foods',
        'country' => 'Saudi Arabia',
        'product_name' => 'Pistachio Chocolate Dates',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
        'message' => 'We would like samples before committing.',
    ]);

    $response->assertRedirect(route('private-label'));

    $inquiry = PrivateLabelInquiry::sole();
    expect($inquiry->full_name)->toBe('Amina Yusuf')
        ->and($inquiry->email)->toBe('amina@example.com')
        ->and($inquiry->phone)->toBe('+966 50 123 4567')
        ->and($inquiry->intent)->toBe('private_label')
        ->and($inquiry->company_name)->toBe('Yusuf Foods')
        ->and($inquiry->country)->toBe('Saudi Arabia')
        ->and($inquiry->product_name)->toBe('Pistachio Chocolate Dates')
        ->and($inquiry->estimated_quantity)->toBe('10,000 units')
        ->and($inquiry->read_at)->toBeNull();
});

test('an inquiry may be sent without a message', function () {
    $response = $this->from(route('private-label'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'intent' => 'private_label',
        'company_name' => 'Yusuf Foods',
        'country' => 'Qatar',
        'product_name' => 'Pistachio Chocolate Dates',
        'product_format' => 'Not sure yet',
        'estimated_quantity' => '5,000 units',
    ]);

    $response->assertRedirect(route('private-label'));
    expect(PrivateLabelInquiry::sole()->message)->toBeNull();
});

test('a private label inquiry requires project details', function () {
    $response = $this->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'intent' => 'private_label',
    ]);

    $response->assertSessionHasErrors([
        'company_name',
        'country',
        'product_name',
        'product_format',
        'estimated_quantity',
    ]);
    expect(PrivateLabelInquiry::count())->toBe(0);
});

test('an inquiry rejects a country outside the supported markets', function () {
    $response = $this->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'intent' => 'private_label',
        'company_name' => 'Yusuf Foods',
        'country' => 'Atlantis',
        'product_name' => 'Pistachio Chocolate Dates',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
    ]);

    $response->assertSessionHasErrors('country');
    expect(PrivateLabelInquiry::count())->toBe(0);
});

test('a general export enquiry only stores contact details and a message', function () {
    $response = $this->from(route('private-label'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'phone' => '+971 50 123 4567',
        'intent' => 'general_export_enquiry',
        'message' => 'We would like to discuss an export opportunity.',
    ]);

    $response->assertRedirect(route('private-label'));

    expect(PrivateLabelInquiry::sole())
        ->intent->toBe('general_export_enquiry')
        ->company_name->toBeNull()
        ->country->toBeNull()
        ->product_name->toBeNull()
        ->product_format->toBeNull()
        ->estimated_quantity->toBeNull();
});
