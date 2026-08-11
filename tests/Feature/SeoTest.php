<?php

use App\Models\Blog;
use App\Models\Product;

/**
 * Inertia skips the root template's head slot whenever SSR answers the request,
 * so the fallback markup is asserted with SSR switched off.
 */
beforeEach(function () {
    config()->set('inertia.ssr.enabled', false);
    config()->set('seo.url', 'https://sevaratrade.com');
    config()->set('seo.image', '/images/about-hero.webp');
});

test('the root template renders the default meta description', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<meta data-inertia="description" name="description" content="'.config('seo.description').'">', false);
});

test('the root template renders a canonical link on the configured domain', function () {
    $response = $this->get(route('about'));

    $response->assertSee('<link data-inertia="canonical" rel="canonical" href="https://sevaratrade.com/about">', false);
});

test('the root template renders open graph and twitter card tags', function () {
    $response = $this->get(route('home'));

    $response->assertSee('property="og:site_name" content="'.config('seo.name').'"', false);
    $response->assertSee('property="og:image" content="https://sevaratrade.com/images/about-hero.webp"', false);
    $response->assertSee('name="twitter:card" content="summary_large_image"', false);
});

test('the root template tells crawlers to index the site', function () {
    $response = $this->get(route('home'));

    $response->assertSee('name="robots" content="index, follow, max-image-preview:large"', false);
});

test('the root template always renders the tags that never vary per page', function () {
    $response = $this->get(route('home'));

    $response->assertSee('name="theme-color" content="#0f1c3f"', false);
    $response->assertSee('property="og:locale" content="'.config('seo.locale').'"', false);
});

test('the seo defaults are shared with every page', function () {
    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
        ->where('seo.url', 'https://sevaratrade.com')
        ->where('seo.name', config('seo.name'))
        ->where('seo.title', config('seo.title'))
        ->where('seo.description', config('seo.description'))
        ->where('seo.image', 'https://sevaratrade.com/images/about-hero.webp')
    );
});

test('the product page shares the data its structured data is built from', function () {
    $product = Product::factory()->create([
        'title_part1' => 'Chocolate',
        'title_part2' => 'Dates',
    ]);

    $response = $this->get(route('products.show', $product));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Product')
        ->where('product.slug', $product->slug)
        ->where('product.title_part1', 'Chocolate')
        ->where('product.title_part2', 'Dates')
        ->has('product.images')
        ->has('product.specifications')
        ->has('seo.url')
    );
});

test('the blog article page shares the data its structured data is built from', function () {
    $blog = Blog::factory()->create([
        'title' => 'Sourcing Premium Dates',
        'author' => 'Sevara Export Team',
    ]);

    $response = $this->get(route('blog.show', $blog));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Blog/Show')
        ->where('blog.slug', $blog->slug)
        ->where('blog.title', 'Sourcing Premium Dates')
        ->where('blog.author', 'Sevara Export Team')
        ->has('blog.created_at')
        ->has('blog.updated_at')
        ->has('seo.url')
    );
});

test('the about and contact pages receive the seo defaults', function (string $route, string $component) {
    $response = $this->get(route($route));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component($component)
        ->has('seo.url')
        ->has('seo.name')
    );
})->with([
    ['about', 'About'],
    ['contact', 'Contact'],
]);

test('the configured site url never carries a trailing slash', function () {
    config()->set('seo.url', rtrim('https://sevaratrade.com/', '/'));

    expect(config('seo.url'))->not->toEndWith('/');
});
