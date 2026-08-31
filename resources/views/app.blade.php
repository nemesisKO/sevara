<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        @php
            $seoUrl = config('seo.url');
            $seoCanonical = $seoUrl.request()->getPathInfo();
            $seoImage = $seoUrl.config('seo.image');
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Never varies per page, so it sits outside the SSR-aware head slot. --}}
        <meta name="theme-color" content="#0f1c3f">
        <meta property="og:locale" content="{{ config('seo.locale') }}">

        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-PVPX3LCL');
        </script>
        <!-- End Google Tag Manager -->

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        {{--
            Fallback head for responses rendered without SSR. Inertia skips this
            slot entirely when SSR is active, so PublicLayout.vue declares the
            same tags for those responses. The data-inertia keys match the
            head-key values there, letting the client adopt rather than duplicate
            them on the first navigation.
        --}}
        <x-inertia::head>
            <title>{{ config('seo.title') }}</title>
            <meta data-inertia="description" name="description" content="{{ config('seo.description') }}">
            <link data-inertia="canonical" rel="canonical" href="{{ $seoCanonical }}">
            <meta data-inertia="robots" name="robots" content="index, follow, max-image-preview:large">

            <meta data-inertia="og:site_name" property="og:site_name" content="{{ config('seo.name') }}">
            <meta data-inertia="og:type" property="og:type" content="website">
            <meta data-inertia="og:title" property="og:title" content="{{ config('seo.title') }}">
            <meta data-inertia="og:description" property="og:description" content="{{ config('seo.description') }}">
            <meta data-inertia="og:url" property="og:url" content="{{ $seoCanonical }}">
            <meta data-inertia="og:image" property="og:image" content="{{ $seoImage }}">

            <meta data-inertia="twitter:card" name="twitter:card" content="summary_large_image">
            <meta data-inertia="twitter:title" name="twitter:title" content="{{ config('seo.title') }}">
            <meta data-inertia="twitter:description" name="twitter:description" content="{{ config('seo.description') }}">
            <meta data-inertia="twitter:image" name="twitter:image" content="{{ $seoImage }}">
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe
                src="https://www.googletagmanager.com/ns.html?id=GTM-PVPX3LCL"
                height="0"
                width="0"
                style="display:none;visibility:hidden"
            ></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->

        <x-inertia::app />
    </body>
</html>
