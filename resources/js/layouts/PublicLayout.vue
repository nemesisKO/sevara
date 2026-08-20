<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from '@/components/public/Footer.vue';
import PublicHeader from '@/components/public/PublicHeader.vue';
import { Toaster } from '@/components/ui/sonner';

const page = usePage();

const seo = computed(() => page.props.seo);

const canonical = computed(
    () => `${seo.value.url}${page.url.split('?')[0] ?? '/'}`,
);
</script>

<template>
    <!--
        Site-wide defaults. SSR is enabled, so the matching data-inertia tags in
        app.blade.php are skipped on those responses and these render instead;
        with SSR off the blade tags render and these adopt them by head-key.
        A page overrides any of them by reusing the same head-key.
    -->
    <Head>
        <meta
            head-key="description"
            name="description"
            :content="seo.description"
        />
        <link head-key="canonical" rel="canonical" :href="canonical" />
        <meta
            head-key="robots"
            name="robots"
            content="index, follow, max-image-preview:large"
        />

        <meta
            head-key="og:site_name"
            property="og:site_name"
            :content="seo.name"
        />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta head-key="og:title" property="og:title" :content="seo.title" />
        <meta
            head-key="og:description"
            property="og:description"
            :content="seo.description"
        />
        <meta head-key="og:url" property="og:url" :content="canonical" />
        <meta head-key="og:image" property="og:image" :content="seo.image" />

        <meta
            head-key="twitter:card"
            name="twitter:card"
            content="summary_large_image"
        />
        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="seo.title"
        />
        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="seo.description"
        />
        <meta
            head-key="twitter:image"
            name="twitter:image"
            :content="seo.image"
        />
    </Head>

    <div class="flex min-h-screen flex-col bg-[#fdfbf6]">
        <Toaster position="top-center" class="public-toaster" />
        <PublicHeader />
        <main class="flex-1">
            <slot />
        </main>
        <Footer />
    </div>
</template>

<style>
.public-toaster {
    top: 50% !important;
    transform: translateX(-50%) !important;
}

.public-toaster [data-sonner-toast] {
    transform: var(--y) translateY(-50%);
}

.public-toaster [data-sonner-toast].newsletter-toast {
    right: auto;
    left: 50%;
    width: max-content;
    transform: var(--y) translate(-50%, -50%);
}

@media (max-width: 600px) {
    .public-toaster {
        left: 50% !important;
        right: auto !important;
        width: min(var(--width), calc(100vw - 2rem)) !important;
    }

    .public-toaster [data-sonner-toast].newsletter-toast {
        width: min(420px, calc(100vw - 2rem));
    }
}
</style>
