<?php

use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use App\Models\Product;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('the contact page renders with the inquiry form options', function () {
    PackagingFormat::factory()->create(['name' => '250g Retail Pouch', 'order' => 2]);
    PackagingFormat::factory()->other()->create(['name' => 'Window Box', 'order' => 5]);
    Product::factory()->create([
        'title_part1' => 'Pistachio',
        'title_part2' => 'Chocolate Dates',
    ]);

    $response = $this->get(route('contact'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Contact')
        ->has('formats', 2)
        ->has('products', 1)
        ->where('products.0.name', 'Pistachio Chocolate Dates')
        ->where('formats.0.name', '250g Retail Pouch')
        ->where('formats.1.name', 'Window Box')
        ->where('markets.0', 'United Arab Emirates')
        ->where('markets.5', 'Bahrain')
        ->where('markets.6', 'Iran')
        ->where('markets.9', 'Pakistan')
    );
});

test('a visitor can send an inquiry from the contact page', function () {
    config()->set('services.n8n.lead_webhook_url', 'https://n8n.test/webhook/sevara-lead');
    config()->set('services.n8n.auth_header_value', 'test-api-key');
    Http::fake(['https://n8n.test/*' => Http::response()]);

    $response = $this->from(route('contact'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
        'phone' => '+971 50 123 4567',
        'intent' => 'distribution_partnership',
        'company_name' => 'Yusuf Foods',
        'country' => 'Qatar',
        'product_name' => 'Pistachio Chocolate Dates',
        'product_format' => '250g Retail Pouch',
        'estimated_quantity' => '10,000 units',
        'message' => 'We would like samples before committing.',
    ]);

    $response->assertRedirect(route('contact'));

    $inquiry = PrivateLabelInquiry::sole();
    expect($inquiry->full_name)->toBe('Amina Yusuf')
        ->and($inquiry->email)->toBe('amina@example.com')
        ->and($inquiry->intent)->toBe('distribution_partnership')
        ->and($inquiry->country)->toBe('Qatar')
        ->and($inquiry->read_at)->toBeNull();

    Http::assertSent(function (Request $request) use ($inquiry): bool {
        return $request->url() === 'https://n8n.test/webhook/sevara-lead'
            && $request->header('X-Api-Key')[0] === 'test-api-key'
            && $request->data() === [
                'submission_id' => $inquiry->id,
                'form_type' => 'distribution_partnership',
                'full_name' => 'Amina Yusuf',
                'company_name' => 'Yusuf Foods',
                'email' => 'amina@example.com',
                'phone' => '+971 50 123 4567',
                'product' => 'Pistachio Chocolate Dates',
                'private_label' => null,
                'country' => 'Qatar',
                'estimated_quantity' => '10,000 units',
                'message' => 'We would like samples before committing.',
            ];
    });
});

test('an invalid inquiry from the contact page returns to the contact page', function () {
    $response = $this->from(route('contact'))->post(route('private-label.inquiries.store'), []);

    $response->assertRedirect(route('contact'));
    $response->assertSessionHasErrors(['full_name', 'email', 'intent', 'country']);
    expect(PrivateLabelInquiry::count())->toBe(0);
});

test('an inquiry requires a valid email address', function () {
    $response = $this->from(route('contact'))->post(route('private-label.inquiries.store'), [
        'full_name' => 'Amina Yusuf',
        'email' => 'not-an-email',
        'intent' => 'sample_request',
        'product_name' => 'Pistachio Chocolate Dates',
    ]);

    $response->assertRedirect(route('contact'));
    $response->assertSessionHasErrors('email');
    expect(PrivateLabelInquiry::count())->toBe(0);
});
