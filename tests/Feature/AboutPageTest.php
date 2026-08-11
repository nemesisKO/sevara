<?php

test('the about page renders', function () {
    $response = $this->get(route('about'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('About'));
});

test('the about page is reachable by guests', function () {
    $response = $this->get('/about');

    $response->assertOk();
});
