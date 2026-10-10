<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import JsonLd from '@/components/JsonLd.vue';
import BlogSection from '@/components/public/BlogSection.vue';
import HeroSlider from '@/components/public/HeroSlider.vue';
import OurBrandNetwork from '@/components/public/OurBrandNetwork.vue';
import OurJourney from '@/components/public/OurJourney.vue';
import PrivateLabelBanner from '@/components/public/PrivateLabelBanner.vue';
import ProductCollection from '@/components/public/ProductCollection.vue';
import WhySevara from '@/components/public/WhySevara.vue';
import type { Blog, Brand, Product, Slide } from '@/types';

defineProps<{
    slides: Slide[];
    brands: Brand[];
    products: Product[];
    blogs: Blog[];
}>();

const page = usePage();

const seo = computed(() => page.props.seo);

const logoUrl = computed(() => page.props.settings.logo_url);

/**
 * Organization and WebSite are declared once here, on the site's root page,
 * so search engines can attach the brand identity to the domain.
 */
const structuredData = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'Organization',
            '@id': `${seo.value.url}/#organization`,
            name: seo.value.name,
            url: `${seo.value.url}/`,
            description: seo.value.description,
            ...(logoUrl.value
                ? { logo: `${seo.value.url}${logoUrl.value}` }
                : {}),
        },
        {
            '@type': 'WebSite',
            '@id': `${seo.value.url}/#website`,
            name: seo.value.name,
            url: `${seo.value.url}/`,
            description: seo.value.description,
            publisher: { '@id': `${seo.value.url}/#organization` },
            inLanguage: 'en',
        },
    ],
}));
</script>

<template>
    <Head title="Sevara Premium Foods" />
    <JsonLd :data="structuredData" head-key="site" />

    <HeroSlider :slides="slides" />
    <WhySevara />
    <ProductCollection :products="products" />
    <PrivateLabelBanner id="p-label-banner" />
    <OurJourney />
    <OurBrandNetwork :brands="brands" />
    <BlogSection :blogs="blogs" />
</template>
