<?php

use App\Models\Blog;

test('the blog index page lists blog posts', function () {
    Blog::factory()->count(3)->create();

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Blog/Index')
        ->has('blogs.data', 3)
    );
});

test('the blog show page displays a post with its blocks and tags', function () {
    $blog = Blog::factory()->create([
        'title' => 'The Future of Chocolate Dates in Global Retail',
        'slug' => 'future-of-chocolate-dates-in-global-retail',
    ]);
    $blog->blocks()->create([
        'type' => 'article',
        'order' => 0,
        'body' => '<p>Premium date confectionery is booming.</p>',
    ]);
    $tag = \App\Models\Tag::factory()->create(['title' => 'Export Guide']);
    $blog->tags()->attach($tag);

    $response = $this->get(route('blog.show', $blog));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Blog/Show')
        ->where('blog.title', 'The Future of Chocolate Dates in Global Retail')
        ->has('blog.blocks', 1)
        ->where('blog.blocks.0.body', '<p>Premium date confectionery is booming.</p>')
        ->has('blog.tags', 1)
        ->where('blog.tags.0.title', 'Export Guide')
    );
});

test('an unknown blog slug 404s', function () {
    $response = $this->get('/blog/does-not-exist');

    $response->assertNotFound();
});

test('the homepage includes the latest blog posts', function () {
    Blog::factory()->count(2)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->has('blogs', 2)
    );
});
