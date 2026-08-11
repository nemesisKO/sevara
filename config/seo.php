<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Canonical Site URL
    |--------------------------------------------------------------------------
    |
    | Absolute origin used to build canonical links, Open Graph URLs and the
    | structured data emitted by the public pages. Kept separate from APP_URL
    | so local development does not advertise localhost to crawlers.
    |
    */

    'url' => rtrim((string) env('SEO_SITE_URL', 'https://sevaratrade.com'), '/'),

    'name' => 'Sevara',

    'title' => 'Sevara — Premium Chocolate Dates',

    'description' => 'Premium chocolate dates with almond centers for importers, distributors and retail chains. Private label and export-ready packaging from Sevara.',

    /*
    |--------------------------------------------------------------------------
    | Social Sharing Image
    |--------------------------------------------------------------------------
    |
    | Root-relative path to the image used for Open Graph and Twitter cards.
    | Resolved against the canonical site URL when rendered.
    |
    */

    'image' => '/images/about-hero.webp',

    'locale' => 'en_US',

];
