<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Box,
    ClipboardList,
    Diamond,
    FlaskConical,
    Gem,
    Globe,
    PackageCheck,
    PenTool,
    Truck,
    Users,
} from '@lucide/vue';
import type { Component } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import PrivateLabelFormats from '@/components/public/PrivateLabelFormats.vue';
import PrivateLabelInquiryForm from '@/components/public/PrivateLabelInquiryForm.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { contact } from '@/routes';
import { request as requestCatalog } from '@/routes/catalog';
import type {
    InquiryProduct,
    PackagingFormat,
    PrivateLabelItem,
} from '@/types';

defineProps<{
    highlights: PrivateLabelItem[];
    steps: PrivateLabelItem[];
    features: PrivateLabelItem[];
    featuredFormats: PackagingFormat[];
    otherFormats: PackagingFormat[];
    markets: string[];
    products: InquiryProduct[];
}>();

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const highlightIcons: Component[] = [PenTool, Box, Globe];
const stepIcons: Component[] = [ClipboardList, FlaskConical, Box, Truck];
const featureIcons: Component[] = [Gem, PackageCheck, Globe];

const projectBenefits = [
    { icon: PackageCheck, label: 'Custom packaging' },
    { icon: FlaskConical, label: 'Flavor development' },
    { icon: Box, label: 'Flexible MOQ' },
    { icon: Users, label: 'End-to-end support' },
];

function fallbackIcon(icons: Component[], index: number): Component {
    return icons[index % icons.length] as Component;
}

function scrollToInquiry(): void {
    document
        .getElementById('private-label-inquiry')
        ?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <Head title="Private Label Chocolate Dates" />

    <section class="overflow-hidden border-b border-[#e7e1d4] bg-[#f2ebe2]">
        <div
            class="grid items-center lg:min-h-152 lg:grid-cols-[1.15fr_0.85fr]"
        >
            <div class="relative order-2 h-full lg:order-1">
                <LazyImage
                    loading="eager"
                    src="/images/private-label-hero.webp"
                    alt="Sevara chocolate dates with custom-branded pouches and gift boxes"
                    class="h-full w-full object-cover object-center"
                />
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 hidden w-24 bg-linear-to-r from-transparent to-[#f2ebe2] lg:block"
                />
            </div>

            <div
                class="order-1 px-6 py-16 lg:order-2 lg:py-20 lg:pr-10 lg:pl-16 xl:pr-14 xl:pl-20"
            >
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-primeGold uppercase"
                >
                    Private Label
                </p>
                <h1
                    class="mt-3 font-serif text-5xl leading-tight text-primeGold sm:text-6xl "
                >
                    Chocolate Dates
                </h1>
                <p
                    class="mt-5 max-w-lg font-serif text-2xl leading-snug text-primeBlue sm:text-3xl"
                >
                    Build Your Own<br class="hidden lg:inline" />
                    Premium Date Confectionery Line.
                </p>
                <p class="mt-5 max-w-md font-serif text-[#6b6558]">
                    Heading build your own premium chocolate line With labor
                    solutions tailored to your brand, market, and audience.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <button
                        type="button"
                        class="inline-flex cursor-pointer items-center gap-2 bg-primeBlue px-6 py-3 text-sm font-medium tracking-wide text-white transition hover:bg-primeBlue/90"
                        @click="scrollToInquiry"
                    >
                        <PenTool class="size-4" />
                        Start Your Project
                    </button>
                    <Link
                        :href="requestCatalog()"
                        class="inline-flex items-center gap-2 border border-primeGold px-6 py-3 text-sm font-medium tracking-wide text-primeGold transition hover:bg-[#b99a45]/10"
                    >
                        <BookOpen class="size-4" />
                        Request Catalog
                    </Link>
                </div>

                <div
                    class="mt-6 grid max-w-lg grid-cols-2 gap-x-5 gap-y-3 text-xs font-medium text-[#5e584d] sm:flex sm:flex-wrap sm:gap-x-5"
                >
                    <span
                        v-for="benefit in projectBenefits"
                        :key="benefit.label"
                        class="inline-flex items-center gap-1.5"
                    >
                        <component
                            :is="benefit.icon"
                            class="size-3.5 text-primeGold"
                            :stroke-width="1.8"
                        />
                        {{ benefit.label }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section v-if="highlights.length > 0" class="border-b border-[#e7e1d4]">
        <div
            class="mx-auto grid max-w-7xl gap-y-12 px-6 py-16 sm:grid-cols-3 sm:divide-x sm:divide-[#e7e1d4] lg:px-10"
        >
            <RevealOnScroll
                v-for="(highlight, index) in highlights"
                :key="highlight.id"
                :delay="index * 120"
            >
                <div class="flex flex-col items-center gap-4 px-6 text-center">
                    <LazyImage
                        v-if="highlight.icon_url"
                        :src="highlight.icon_url"
                        alt=""
                        class="size-10 object-contain"
                    />
                    <component
                        :is="fallbackIcon(highlightIcons, index)"
                        v-else
                        class="size-10 text-[#b99a45]"
                        :stroke-width="1.1"
                    />
                    <h2 class="font-serif text-xl text-primeBlue">
                        {{ highlight.title }}
                    </h2>
                    <p class="text-sm text-[#6b6558]">
                        {{ highlight.description }}
                    </p>
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <PrivateLabelFormats
        :featured-formats="featuredFormats"
        :other-formats="otherFormats"
    />

    <PrivateLabelInquiryForm
        :markets="markets"
        :formats="[...featuredFormats, ...otherFormats]"
        :products="products"
    />

    <section v-if="steps.length > 0" class="bg-[#fdfbf6] py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <RevealOnScroll>
                <div class="text-center">
                    <h2 class="font-serif text-3xl text-primeBlue sm:text-4xl">
                        From Idea to Export
                    </h2>
                    <div class="mt-5 flex items-center justify-center gap-4">
                        <span class="h-px w-12 bg-primeGold/40" />
                        <Diamond class="size-2.5 text-primeGold" />
                        <span class="h-px w-12 bg-primeGold/40" />
                    </div>
                </div>
            </RevealOnScroll>

            <div class="relative mt-14">
                <span
                    class="absolute top-9 right-[10%] left-[10%] hidden h-px bg-primeGold/30 lg:block"
                />

                <div class="relative grid gap-12 sm:grid-cols-2 lg:grid-cols-4">
                    <RevealOnScroll
                        v-for="(item, index) in steps"
                        :key="item.id"
                        :delay="index * 120"
                    >
                        <div
                            class="flex flex-col items-center gap-4 text-center"
                        >
                            <span
                                class="flex size-18 items-center justify-center rounded-full border border-primeGold/50 bg-[#fdfbf6] text-primeGold"
                            >
                                <LazyImage
                                    v-if="item.icon_url"
                                    :src="item.icon_url"
                                    alt=""
                                    class="size-8 object-contain"
                                />
                                <component
                                    :is="fallbackIcon(stepIcons, index)"
                                    v-else
                                    class="size-7"
                                    :stroke-width="1.2"
                                />
                            </span>
                            <div>
                                <p class="font-serif text-xl text-primeGold">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold tracking-[0.2em] text-primeBlue uppercase"
                                >
                                    {{ item.title }}
                                </p>
                            </div>
                            <p class="max-w-56 font-serif text-[#6b6558]">
                                {{ item.description }}
                            </p>
                        </div>
                    </RevealOnScroll>
                </div>
            </div>
        </div>
    </section>

    <section v-if="features.length > 0" class="bg-[#f7f3e8]/60 py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <RevealOnScroll>
                <div class="text-center">
                    <h2 class="font-serif text-3xl text-primeBlue sm:text-4xl">
                        Built for Reliable B2B Supply
                    </h2>
                    <div class="mt-5 flex items-center justify-center gap-4">
                        <span class="h-px w-12 bg-primeGold/40" />
                        <Diamond class="size-2.5 text-primeGold" />
                        <span class="h-px w-12 bg-primeGold/40" />
                    </div>
                </div>
            </RevealOnScroll>

            <div class="mt-14 grid gap-8 sm:grid-cols-3">
                <RevealOnScroll
                    v-for="(feature, index) in features"
                    :key="feature.id"
                    :delay="index * 120"
                >
                    <div class="flex h-full items-start gap-4">
                        <span
                            class="flex size-12 shrink-0 items-center justify-center rounded-full border border-primeGold/50 text-[#b99a45]"
                        >
                            <LazyImage
                                v-if="feature.icon_url"
                                :src="feature.icon_url"
                                alt=""
                                class="size-6 object-contain"
                            />
                            <component
                                :is="fallbackIcon(featureIcons, index)"
                                v-else
                                class="size-5"
                                :stroke-width="1.3"
                            />
                        </span>
                        <div>
                            <h3
                                class="font-serif text-base font-semibold text-primeBlue"
                            >
                                {{ feature.title }}
                            </h3>
                            <p class="mt-2 text-sm text-[#6b6558]">
                                {{ feature.description }}
                            </p>
                        </div>
                    </div>
                </RevealOnScroll>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-primeBlue py-16">
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
            <h2 class="max-w-lg font-serif text-3xl text-primeGold sm:text-4xl">
                Ready to build your chocolate date line?
            </h2>
            <Link
                :href="contact()"
                class="inline-flex shrink-0 items-center gap-2 bg-primeGold px-7 py-3.5 text-sm font-medium tracking-wide text-primeBlue transition hover:bg-[#c9a233]"
            >
                <Users class="size-4" />
                Contact Export Team
            </Link>
        </div>
    </section>
</template>
