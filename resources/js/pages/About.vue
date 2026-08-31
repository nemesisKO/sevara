<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ClipboardCheck,
    Coffee,
    ConciergeBell,
    Diamond,
    Gift,
    Globe,
    Package,
    ShoppingCart,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import JsonLd from '@/components/JsonLd.vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { contact, privateLabel } from '@/routes';
import { request as requestCatalog } from '@/routes/catalog';
import { index as productsIndex } from '@/routes/products';

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const page = usePage();

const seo = computed(() => page.props.seo);

const metaDescription =
    'Sevara is a modern premium FMCG brand crafting chocolate-coated dates with almond centers for importers, distributors and retail chains worldwide.';

const structuredData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'AboutPage',
    '@id': `${seo.value.url}/about#webpage`,
    url: `${seo.value.url}/about`,
    name: `About ${seo.value.name}`,
    description: metaDescription,
    isPartOf: { '@id': `${seo.value.url}/#website` },
    about: { '@id': `${seo.value.url}/#organization` },
    primaryImageOfPage: `${seo.value.url}/images/about-hero.webp`,
    inLanguage: 'en',
}));

const standards = [
    {
        title: 'Selected Dates',
        description: 'Carefully chosen for natural sweetness and texture.',
        image: '/images/standard-dates.webp',
    },
    {
        title: 'Almond Center',
        description:
            'Crisp and smooth almonds for a balanced and satisfying bite.',
        image: '/images/standard-almonds.webp',
    },
    {
        title: 'Refined Chocolate Coating',
        description:
            'Smooth, refined chocolate for elegance and depth of flavor.',
        image: '/images/standard-chocolate.webp',
    },
];

const processSteps = [
    {
        title: 'Select',
        description:
            'We source quality dates and almonds from trusted global partners.',
    },
    {
        title: 'Coat',
        description:
            'Expert craftsmanship to create the perfect chocolate date.',
    },
    {
        title: 'Pack',
        description:
            'Packed with care in elegant packaging that protects and impresses.',
    },
    {
        title: 'Prepare for Export',
        description:
            'Export-ready operations to deliver reliability to your market.',
    },
];

const tradePartners = [
    { icon: Globe, title: 'Importers' },
    { icon: Users, title: 'Distributors' },
    { icon: ShoppingCart, title: 'Retail Chains' },
    { icon: Gift, title: 'Gift Retail' },
    { icon: ConciergeBell, title: 'Hotels' },
    { icon: Coffee, title: 'Coffee Shops' },
];

const reliabilityPoints = [
    {
        icon: Globe,
        title: 'Export-Focused Communication',
        description: 'Clear logistics, documentation and on-time delivery.',
    },
    {
        icon: Package,
        title: 'Retail-Ready Packaging Options',
        description: 'Flexible formats and packaging to suit your positioning.',
    },
    {
        icon: ClipboardCheck,
        title: 'Private Label Project Support',
        description: 'From concept and artwork to production and export.',
    },
];
</script>

<template>
    <Head title="About Sevara">
        <meta
            head-key="description"
            name="description"
            :content="metaDescription"
        />
        <meta
            head-key="og:description"
            property="og:description"
            :content="metaDescription"
        />
        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="metaDescription"
        />
    </Head>
    <JsonLd :data="structuredData" head-key="about" />

    <section class="relative overflow-hidden bg-[#fdfbf6]">
        <div
            class="relative h-64 w-full sm:h-80 lg:absolute lg:inset-0 lg:h-auto"
        >
            <LazyImage
                loading="eager"
                src="/images/about-hero.webp"
                alt="Sevara chocolate dates with almond beside a premium pouch"
                class="h-full w-full object-cover object-right lg:object-center"
            />
            <div
                class="pointer-events-none absolute inset-0 hidden bg-linear-to-r from-[#fdfbf6] via-[#fdfbf6]/70 to-transparent to-72% lg:block"
            />
        </div>

        <div
            class="relative mx-auto max-w-7xl px-6 py-14 lg:px-10 lg:py-32 xl:py-40"
        >
            <div class="max-w-lg">
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    About Sevara
                </p>
                <h1
                    class="mt-4 font-serif text-4xl leading-tight text-primeBlue sm:text-5xl xl:text-6xl"
                >
                    Quiet Luxury in Date Confectionery
                </h1>

                <div class="mt-7 flex items-center gap-4">
                    <span class="h-px w-16 bg-[#d4af37]/50" />
                    <Diamond class="size-3 text-primeGold" />
                    <span class="h-px w-16 bg-[#d4af37]/50" />
                </div>

                <p class="mt-7 max-w-sm text-[#3a3a35]">
                    A modern premium FMCG brand crafted for global trade
                    partners.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <Link
                        :href="productsIndex()"
                        class="inline-flex items-center gap-2 bg-primeBlue px-6 py-3 text-xs font-semibold tracking-[0.15em] text-white uppercase transition hover:bg-primeBlue/90"
                    >
                        Explore Products
                    </Link>
                    <Link
                        :href="contact()"
                        class="inline-flex items-center gap-2 border border-primeBlue/30 px-6 py-3 text-xs font-semibold tracking-[0.15em] text-primeBlue uppercase transition hover:bg-primeBlue/5"
                    >
                        Contact Export Team
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#fdfbf6] py-12 lg:py-16">
        <div
            class="mx-auto grid max-w-6xl items-center gap-12 px-6 lg:grid-cols-2 lg:gap-16 lg:px-10"
        >
            <RevealOnScroll>
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    Our Philosophy
                </p>
                <h2
                    class="mt-4 font-serif text-3xl leading-snug text-primeBlue sm:text-4xl"
                >
                    Premium Begins Before the First Bite
                </h2>

                <div class="mt-6 flex items-center gap-4">
                    <span class="h-px w-16 bg-[#d4af37]/40" />
                    <Diamond class="size-2.5 text-primeGold" />
                    <span class="h-px w-16 bg-[#d4af37]/40" />
                </div>

                <p class="mt-6 max-w-md font-serif text-[#6b6558]">
                    Sevara brings together selected dates, almond centers,
                    refined chocolate coating and elegant packaging to create a
                    product ready for modern retail and international
                    distribution.
                </p>
            </RevealOnScroll>

            <RevealOnScroll :delay="150">
                <div
                    class="aspect-3/2 overflow-hidden rounded-sm border border-[#e7e1d4]"
                >
                    <LazyImage
                        src="/images/philosophy-chocolate-date.webp"
                        alt="Cross-section of a Sevara chocolate date with a whole almond center"
                        class="h-full w-full object-cover"
                    />
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <section class="bg-[#f7f3e8]/70 py-12 lg:py-16">
        <div
            class="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-[0.85fr_1.15fr] lg:items-center lg:gap-14 lg:px-10"
        >
            <RevealOnScroll>
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    Standards
                </p>
                <h2
                    class="mt-4 font-serif text-3xl leading-snug text-primeBlue sm:text-4xl"
                >
                    The Sevara Standard
                </h2>

                <div class="mt-6 space-y-1.5 font-serif text-[#6b6558]">
                    <p>Every element is chosen with care.</p>
                    <p>Nothing is left to chance.</p>
                    <p>The details define the experience.</p>
                </div>
            </RevealOnScroll>

            <div class="grid gap-3 sm:grid-cols-3">
                <RevealOnScroll
                    v-for="(standard, index) in standards"
                    :key="standard.title"
                    :delay="index * 120"
                >
                    <article
                        class="relative flex aspect-4/3 flex-col justify-end overflow-hidden rounded-sm bg-primeBlue transition duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.02] hover:shadow-[0_20px_45px_-24px_rgba(15,28,63,0.55)] motion-reduce:hover:translate-y-0 motion-reduce:hover:scale-100 sm:aspect-9/16"
                    >
                        <LazyImage
                            :src="standard.image"
                            :alt="standard.title"
                            class="absolute inset-0 h-full w-full object-cover"
                        />
                        <div
                            class="pointer-events-none absolute inset-0 bg-linear-to-t from-black/90 via-black/45 to-black/10"
                        />
                        <div class="relative p-5 text-center">
                            <h3
                                class="text-xs font-semibold tracking-[0.2em] text-white uppercase"
                            >
                                {{ standard.title }}
                            </h3>
                            <div
                                class="mt-3 flex items-center justify-center gap-2"
                            >
                                <span class="h-px w-8 bg-[#d4af37]/50" />
                                <Diamond class="size-2 text-[#d4af37]" />
                                <span class="h-px w-8 bg-[#d4af37]/50" />
                            </div>
                            <p class="mt-3 text-xs text-[#c9c2b4]">
                                {{ standard.description }}
                            </p>
                        </div>
                    </article>
                </RevealOnScroll>
            </div>
        </div>
    </section>

    <section class="bg-[#fdfbf6] py-20 ">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <RevealOnScroll>
                <p
                    class="text-center font-serif text-sm font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    From Ingredient to International Shelf
                </p>
            </RevealOnScroll>

            <div class="relative mt-12">
                <span
                    class="absolute top-2 right-[12%] left-[12%] hidden h-px bg-[#d4af37]/40 sm:block"
                />
                <span
                    class="absolute top-2 left-1/2 hidden size-2 -translate-x-1/2 -translate-y-1/2 rotate-45 border border-[#d4af37]/60 bg-[#fdfbf6] sm:block"
                />

                <div
                    class="relative grid gap-12 font-serif sm:grid-cols-2 lg:grid-cols-4"
                >
                    <RevealOnScroll
                        v-for="(step, index) in processSteps"
                        :key="step.title"
                        :delay="index * 110"
                    >
                        <div
                            class="flex flex-col items-center gap-3 text-center"
                        >
                            <span
                                class="hidden size-4 rounded-full border-2 border-[#d4af37] bg-[#fdfbf6] sm:block"
                            />
                            <p class="font-serif text-lg text-primeGold">
                                {{ String(index + 1).padStart(2, '0') }}
                            </p>
                            <p
                                class="text-sm font-semibold tracking-[0.2em] text-primeBlue uppercase"
                            >
                                {{ step.title }}
                            </p>
                            <p class="max-w-52 text-sm text-[#6b6558]">
                                {{ step.description }}
                            </p>
                        </div>
                    </RevealOnScroll>
                </div>
            </div>
        </div>
    </section>

    <section
        class="relative overflow-hidden bg-primeBlue py-20 lg:py-28 xl:py-36"
    >
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-32 -left-40 size-136 -rotate-12 opacity-[0.07]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -right-40 -bottom-32 size-120 rotate-18 opacity-[0.06]"
                :style="{ background: mandala }"
            />
        </div>

        <div class="relative z-10 w-full px-6 lg:pl-16 xl:pl-24 2xl:pl-32">
            <RevealOnScroll>
                <div class="lg:max-w-lg">
                    <h2
                        class="font-serif text-4xl leading-tight text-primeGold sm:text-5xl"
                    >
                        Premium Dates.<br />
                        Modern Packaging.<br />
                        International Ambition.
                    </h2>

                    <div class="mt-8 flex items-center gap-4">
                        <span class="h-px w-32 bg-[#d4af37]/40 md:w-48" />
                        <Diamond class="size-3 text-primeGold" />
                        <span class="h-px w-32 bg-[#d4af37]/40 md:w-48" />
                    </div>

                    <Link
                        :href="privateLabel()"
                        class="mt-8 inline-flex items-center gap-2 border border-[#d4af37]/60 px-7 py-3.5 text-xs font-semibold tracking-[0.15em] text-[#e9d9ae] uppercase transition hover:bg-[#d4af37]/10"
                    >
                        Partner with Sevara
                    </Link>
                </div>
            </RevealOnScroll>
        </div>

        <div
            class="relative mt-12 aspect-7/5 w-full lg:absolute lg:inset-y-0 lg:right-0 lg:mt-0 lg:aspect-auto lg:w-1/2 lg:max-w-4xl"
        >
            <LazyImage
                src="/images/international-ambition.webp"
                alt="Sevara chocolate date pouch, gift box and flow pack against a gold globe"
                class="h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-primeBlue/20" />
            <div
                class="absolute inset-0 hidden bg-linear-to-r from-primeBlue to-transparent to-30% lg:block"
            />
        </div>
    </section>

    <section class="bg-[#fdfbf6] py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <RevealOnScroll>
                <p
                    class="text-center text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    Built for Modern Trade Partners
                </p>
            </RevealOnScroll>

            <div
                class="mt-12 grid grid-cols-2 gap-y-10 sm:grid-cols-3 sm:divide-x sm:divide-[#e7e1d4] lg:grid-cols-6"
            >
                <RevealOnScroll
                    v-for="(partner, index) in tradePartners"
                    :key="partner.title"
                    :delay="index * 90"
                >
                    <div
                        class="flex flex-col items-center gap-4 px-4 text-center"
                    >
                        <component
                            :is="partner.icon"
                            class="size-9 text-primeGold"
                            :stroke-width="1.1"
                        />
                        <p
                            class="text-[11px] font-semibold tracking-[0.2em] text-primeBlue uppercase"
                        >
                            {{ partner.title }}
                        </p>
                    </div>
                </RevealOnScroll>
            </div>
        </div>
    </section>

    <!-- #f9f5ec is the previous bg-[#f7f3e8]/70 flattened onto the page, so the photo can fade into it. -->
    <section
        class="relative overflow-hidden bg-[#f9f5ec] py-12 lg:py-12 xl:py-14"
    >
        <div class="relative z-10 w-full px-6 lg:pl-16 xl:pl-24 2xl:pl-32">
            <RevealOnScroll>
                <div class="lg:max-w-lg">
                    <p
                        class="text-sm font-semibold tracking-[0.3em] text-primeGold uppercase"
                    >
                        Reliable by Design
                    </p>
                    <h2
                        class="mt-4 font-serif text-4xl leading-snug text-primeBlue sm:text-5xl"
                    >
                        Reliable by Design
                    </h2>

                    <div class="mt-9 flex flex-col gap-8 font-serif">
                        <div
                            v-for="point in reliabilityPoints"
                            :key="point.title"
                            class="flex items-start gap-4"
                        >
                            <component
                                :is="point.icon"
                                class="mt-0.5 size-8 shrink-0 text-primeGold"
                                :stroke-width="1.1"
                            />
                            <div>
                                <h3
                                    class="text-sm font-semibold tracking-[0.2em] text-primeBlue uppercase"
                                >
                                    {{ point.title }}
                                </h3>
                                <p class="mt-2 text-base text-[#6b6558]">
                                    {{ point.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </RevealOnScroll>
        </div>

        <div
            class="relative mt-10 aspect-7/5 w-full lg:absolute lg:inset-y-12 lg:right-0 lg:mt-0 lg:aspect-auto lg:w-[42%] lg:max-w-3xl xl:inset-y-16"
        >
            <LazyImage
                src="/images/private-label-kit-box.webp"
                alt="Sevara private label kit box with unbranded pouch and sample sachets"
                class="h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 hidden lg:block" />
        </div>
    </section>

    <section class="relative overflow-hidden bg-[#fdfbf6] py-20 lg:py-24">
        <div
            class="pointer-events-none absolute -top-20 -left-40 size-120 -rotate-12 opacity-[0.05] grayscale"
            :style="{ background: mandala }"
        />

        <div class="relative mx-auto max-w-5xl px-6 lg:px-10">
            <RevealOnScroll>
                <div
                    class="flex flex-col items-center gap-10 lg:flex-row lg:items-center lg:gap-14"
                >
                    <div class="lg:flex-1">
                        <h2
                            class="text-center font-serif text-xl tracking-[0.25em] text-primeBlue uppercase sm:text-2xl"
                        >
                            A Note from Sevara
                        </h2>

                        <div
                            class="mt-5 flex items-center justify-center gap-3"
                        >
                            <span class="h-px flex-1 bg-[#d4af37]/40" />
                            <Diamond class="size-2.5 text-primeGold" />
                            <span class="h-px flex-1 bg-[#d4af37]/40" />
                        </div>

                        <p
                            class="mt-7 text-center leading-relaxed text-[#6b6558] font-serif"
                        >
                            We believe a premium product is built through taste,
                            packaging, consistency and trust. Our role is to
                            help partners introduce chocolate dates with the
                            confidence of a modern international brand.
                        </p>
                    </div>

                    <!-- Gold-on-white artwork: multiply drops the white box onto the cream background. -->
                    <LazyImage
                        src="/images/signature-export-team.webp"
                        alt="Signed, Sevara Export Team"
                        class="w-64 shrink-0 mix-blend-multiply sm:w-72 lg:w-80"
                    />
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <section class="relative mb-16 overflow-hidden bg-[#fdfbf6] py-16">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-20 -left-32 size-96 -rotate-12 opacity-[0.04]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -right-32 -bottom-24 size-96 rotate-18 opacity-[0.04]"
                :style="{ background: mandala }"
            />
        </div>

        <div
            class="relative mx-auto flex max-w-6xl flex-col items-center gap-8 px-6 text-center lg:flex-row lg:justify-between lg:px-10 lg:text-left"
        >
            <h2 class="max-w-lg font-serif text-3xl text-primeBlue sm:text-4xl">
                Ready to introduce Sevara to your market?
            </h2>

            <div class="flex flex-wrap justify-center gap-4">
                <Link
                    :href="requestCatalog()"
                    class="inline-flex items-center gap-2 border border-primeBlue/30 px-6 py-3.5 text-xs font-semibold tracking-[0.15em] text-primeBlue uppercase transition hover:bg-primeBlue/5"
                >
                    <BookOpen class="size-4" />
                    Request Catalog
                </Link>
                <Link
                    :href="contact()"
                    class="inline-flex items-center gap-2 border border-primeBlue/30 px-6 py-3.5 text-xs font-semibold tracking-[0.15em] text-primeBlue uppercase transition hover:bg-primeBlue/5"
                >
                    <Users class="size-4" />
                    Contact Export Team
                </Link>
            </div>
        </div>
    </section>
</template>
