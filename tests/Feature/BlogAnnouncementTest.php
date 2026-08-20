<?php

use App\Mail\BlogPublished;
use App\Models\Blog;

test('a blog announcement contains the blog title, main picture and read more link', function () {
    $blog = Blog::factory()->create([
        'title' => 'A New Sevara Story',
        'image_path' => 'blogs/new-story.jpg',
    ]);

    $html = (new BlogPublished($blog))->render();

    expect($html)
        ->toContain('A New Sevara Story')
        ->toContain(url($blog->image_url))
        ->toContain(route('blog.show', ['blog' => $blog->slug]));
});
