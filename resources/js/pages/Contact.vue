<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Clock,
    Diamond,
    Mail,
    MapPin,
    PenTool,
    Phone,
} from '@lucide/vue';
import { computed } from 'vue';
import JsonLd from '@/components/JsonLd.vue';
import LazyImage from '@/components/LazyImage.vue';
import PrivateLabelInquiryForm from '@/components/public/PrivateLabelInquiryForm.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { privateLabel } from '@/routes';
import { request as requestCatalog } from '@/routes/catalog';
import type { PackagingFormat } from '@/types';

defineProps<{
    markets: string[];
    formats: PackagingFormat[];
}>();

const mandala = "url('/images/mandala.png') center / contain no-repeat";

/** Single source for the details shown on the page and published as structured data. */
const contactEmail = 'info@sevaradates.com';
const contactPhone = '+971 50 123 4567';
const contactPhoneHref = 'tel:+971501234567';
const officeCity = 'Dubai';
const officeCountry = 'AE';

const channels = [
    {
        icon: Mail,
        title: 'Email',
        value: contactEmail,
        href: `mailto:${contactEmail}`,
        description: 'For catalogs, pricing and private label projects.',
    },
    {
        icon: Phone,
        title: 'Phone',
        value: contactPhone,
        href: contactPhoneHref,
        description: 'Sunday to Thursday, 09:00 – 18:00 (GST).',
    },
    {
        icon: MapPin,
        title: 'Office',
        value: 'Dubai, United Arab Emirates',
        href: null,
        description: 'Export operations for global trade partners.',
    },
];

const page = usePage();

const seo = computed(() => page.props.seo);

const metaDescription =
    'Contact the Sevara export team for catalogs, pricing, private label projects and distribution enquiries. Based in Dubai, shipping worldwide.';

const structuredData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'ContactPage',
    '@id': `${seo.value.url}/contact#webpage`,
    url: `${seo.value.url}/contact`,
    name: `Contact ${seo.value.name}`,
    description: metaDescription,
    isPartOf: { '@id': `${seo.value.url}/#website` },
    about: { '@id': `${seo.value.url}/#organization` },
    mainEntity: {
        '@type': 'Organization',
        '@id': `${seo.value.url}/#organization`,
        name: seo.value.name,
        email: contactEmail,
        telephone: contactPhone,
        address: {
            '@type': 'PostalAddress',
            addressLocality: officeCity,
            addressCountry: officeCountry,
        },
        contactPoint: [
            {
                '@type': 'ContactPoint',
                contactType: 'sales',
                email: contactEmail,
                telephone: contactPhone,
                areaServed: 'Worldwide',
                availableLanguage: ['English'],
            },
        ],
    },
    inLanguage: 'en',
}));

const nextSteps = [
    {
        title: 'We Review',
        description:
            'Your inquiry reaches the export team the same working day.',
    },
    {
        title: 'We Reply',
        description:
            'Expect a response with pricing and lead times within two business days.',
    },
    {
        title: 'We Sample',
        description:
            'Samples, artwork and production planning follow once the brief is agreed.',
    },
];

function scrollToInquiry(): void {
    document
        .getElementById('contact-inquiry')
        ?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <Head title="Contact Sevara">
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
    <JsonLd :data="structuredData" head-key="contact" />

    <section
        class="relative overflow-hidden bg-[#fdfbf6] py-16 lg:py-32 xl:py-40"
    >
        <div class="relative z-10 w-full px-6 lg:pl-14 xl:pl-20 2xl:pl-28">
            <div class="lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    Contact
                </p>
                <h1
                    class="mt-4 font-serif text-4xl leading-tight text-[#0f1c3f] sm:text-5xl xl:text-6xl"
                >
                    Talk to the Sevara Export Team
                </h1>

                <div class="mt-7 flex items-center gap-4">
                    <span class="h-px w-16 bg-[#d4af37]/50" />
                    <Diamond class="size-3 text-[#d4af37]" />
                    <span class="h-px w-16 bg-[#d4af37]/50" />
                </div>

                <p class="mt-7 max-w-sm text-[#3a3a35]">
                    Distribution, retail listings or a private label line — tell
                    us about your market and we will take it from there.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <button
                        type="button"
                        class="inline-flex cursor-pointer items-center gap-2 bg-[#0f1c3f] px-6 py-3 text-xs font-semibold tracking-[0.15em] text-white uppercase transition hover:bg-[#0f1c3f]/90"
                        @click="scrollToInquiry"
                    >
                        <PenTool class="size-4" />
                        Send an Inquiry
                    </button>
                    <a
                        :href="`mailto:${contactEmail}`"
                        class="inline-flex items-center gap-2 border border-[#0f1c3f]/30 px-6 py-3 text-xs font-semibold tracking-[0.15em] text-[#0f1c3f] uppercase transition hover:bg-[#0f1c3f]/5"
                    >
                        <Mail class="size-4" />
                        Email Us
                    </a>
                </div>
            </div>
        </div>

        <div
            class="relative mt-12 aspect-square w-full lg:absolute lg:inset-y-0 lg:right-0 lg:mt-0 lg:aspect-auto lg:w-1/2"
        >
            <LazyImage
                loading="eager"
                src="/images/gift-collection.webp"
                alt="Sevara premium date collection gift box with chocolate dates and almonds"
                class="h-full w-full object-cover object-center"
            />
            <div
                class="absolute inset-0 hidden bg-linear-to-r from-[#fdfbf6] via-[#fdfbf6]/40 via-7% to-transparent to-18% lg:block"
            />
        </div>
    </section>

    <section class="bg-[#fdfbf6] py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <div
                class="grid gap-y-12 sm:grid-cols-3 sm:divide-x sm:divide-[#e7e1d4]"
            >
                <RevealOnScroll
                    v-for="(channel, index) in channels"
                    :key="channel.title"
                    :delay="index * 120"
                >
                    <div
                        class="flex h-full flex-col items-center gap-4 px-6 text-center"
                    >
                        <component
                            :is="channel.icon"
                            class="size-9 text-primeGold"
                            :stroke-width="1.1"
                        />
                        <p
                            class="text-[11px] font-semibold tracking-[0.2em] text-[#0f1c3f] uppercase"
                        >
                            {{ channel.title }}
                        </p>
                        <a
                            v-if="channel.href"
                            :href="channel.href"
                            class="font-serif text-lg text-[#0f1c3f] underline-offset-4 transition-colors hover:text-primeGold hover:underline"
                        >
                            {{ channel.value }}
                        </a>
                        <p v-else class="font-serif text-lg text-[#0f1c3f]">
                            {{ channel.value }}
                        </p>
                        <p class="max-w-56 text-sm text-[#6b6558]">
                            {{ channel.description }}
                        </p>
                    </div>
                </RevealOnScroll>
            </div>
        </div>
    </section>

    <PrivateLabelInquiryForm
        :markets="markets"
        :formats="formats"
        section-id="contact-inquiry"
        heading="Tell Us About Your Project"
        description="Share your market, packaging needs and target quantity. Our export team will review and contact you."
        submit-label="Send Inquiry"
        panel-label="Export Enquiries"
        panel-image="/images/private-label-kit.webp"
    />

    <section class="bg-[#f7f3e8] py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <RevealOnScroll>
                <div class="text-center">
                    <p
                        class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                    >
                        Your Inquiry Journey
                    </p>
                    <h2
                        class="mt-4 font-serif text-4xl leading-tight text-primeBlue sm:text-5xl"
                    >
                        What Happens Next
                    </h2>
                    <div class="mt-5 flex items-center justify-center gap-4">
                        <span class="h-px w-14 bg-[#d4af37]/45" />
                        <Diamond class="size-3 text-[#d4af37]" />
                        <span class="h-px w-14 bg-[#d4af37]/45" />
                    </div>
                    <p
                        class="mx-auto mt-5 max-w-xl text-base leading-7 text-[#625d52]"
                    >
                        From your first message to sample approval, our export
                        team keeps every step clear and personal.
                    </p>
                </div>
            </RevealOnScroll>

            <div class="mt-14 grid gap-5 sm:grid-cols-3 sm:gap-6">
                <RevealOnScroll
                    v-for="(step, index) in nextSteps"
                    :key="step.title"
                    :delay="index * 120"
                >
                    <article
                        class="flex h-full flex-col items-center border border-[#d4af37]/25 bg-[#fdfbf6] px-6 py-8 text-center shadow-[0_16px_34px_-30px_rgba(15,28,63,0.7)] sm:px-7"
                    >
                        <span
                            class="flex size-13 items-center justify-center rounded-full border border-[#d4af37]/50 bg-[#f7f3e8] font-serif text-2xl text-primeGold"
                        >
                            {{ String(index + 1).padStart(2, '0') }}
                        </span>
                        <h3
                            class="mt-6 text-sm font-bold tracking-[0.18em] text-primeBlue uppercase"
                        >
                            {{ step.title }}
                        </h3>
                        <span class="mt-4 h-px w-10 bg-[#d4af37]/45" />
                        <p
                            class="mt-4 max-w-60 text-[15px] leading-7 text-[#625d52]"
                        >
                            {{ step.description }}
                        </p>
                    </article>
                </RevealOnScroll>
            </div>

            <RevealOnScroll :delay="360">
                <div
                    class="mx-auto mt-14 flex w-fit items-center justify-center gap-3 border-y border-[#d4af37]/25 px-5 py-4 text-sm font-medium text-primeBlue sm:text-base"
                >
                    <Clock class="size-5 text-primeGold" :stroke-width="1.5" />
                    <span>
                        Replies within two business days, Sunday to Thursday.
                    </span>
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <section class="relative overflow-hidden bg-[#0f1c3f] py-16">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-20 -left-32 size-96 -rotate-12 opacity-[0.07]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -right-32 -bottom-24 size-96 rotate-18 opacity-[0.06]"
                :style="{ background: mandala }"
            />
        </div>

        <div
            class="relative mx-auto flex max-w-6xl flex-col items-center gap-8 px-6 text-center lg:flex-row lg:justify-between lg:px-10 lg:text-left"
        >
            <h2 class="max-w-lg font-serif text-3xl text-[#e9d9ae] sm:text-4xl">
                Prefer to explore before you write?
            </h2>

            <div class="flex flex-wrap justify-center gap-4">
                <Link
                    :href="requestCatalog()"
                    class="inline-flex items-center gap-2 border border-[#d4af37]/60 bg-[#d4af37]/10 px-6 py-3.5 text-xs font-semibold tracking-[0.15em] text-[#e9d9ae] uppercase transition hover:bg-[#d4af37]/20"
                >
                    <BookOpen class="size-4" />
                    Request Catalog
                </Link>
                <Link
                    :href="privateLabel()"
                    class="inline-flex items-center gap-2 border border-[#d4af37]/60 px-6 py-3.5 text-xs font-semibold tracking-[0.15em] text-[#e9d9ae] uppercase transition hover:bg-[#d4af37]/10"
                >
                    <PenTool class="size-4" />
                    Private Label
                </Link>
            </div>
        </div>
    </section>
</template>
