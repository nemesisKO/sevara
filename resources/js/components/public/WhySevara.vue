<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Boxes,
    Diamond,
    Gift,
    Globe,
    Nut,
    Package,
    ShoppingBag,
} from '@lucide/vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import { onBeforeUnmount, onMounted, useTemplateRef } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { contact } from '@/routes';

const exportEnquiryHref = `${contact.url({ query: { intent: 'general_export_enquiry' } })}#contact-inquiry-form`;

const features = [
    { icon: Nut, title: 'Premium Ingredients' },
    { icon: Gift, title: 'Elegant Packaging' },
    { icon: Package, title: 'Private Label Capability' },
    { icon: Globe, title: 'Export-Focused Support' },
    { icon: ShoppingBag, title: 'Retail-Ready Formats' },
    { icon: Boxes, title: 'Consistent Supply' },
];

const headline = useTemplateRef('headline');

let split: SplitText | null = null;
let tween: gsap.core.Tween | null = null;

/**
 * Reveal the headline word by word once the section scrolls into view. The
 * words rise out of a mask clipped to each line.
 */
onMounted(async () => {
    if (
        !headline.value ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    gsap.registerPlugin(ScrollTrigger, SplitText);

    // Splitting before the webfont lands would measure the wrong line breaks.
    await document.fonts?.ready;

    split = SplitText.create(headline.value, {
        type: 'lines,words',
        mask: 'lines',
    });

    tween = gsap.from(split.words, {
        yPercent: 120,
        opacity: 0,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.045,
        scrollTrigger: {
            trigger: headline.value,
            start: 'top 85%',
            once: true,
        },
    });
});

onBeforeUnmount(() => {
    tween?.scrollTrigger?.kill();
    tween?.kill();
    split?.revert();
});
</script>

<template>
    <section class="relative overflow-hidden bg-[#f7f3e8]">
        <div
            class="pointer-events-none absolute top-10 right-10 bottom-0 hidden aspect-1234/1275 max-w-[56%] xl:block"
        >
            <LazyImage
                src="/images/gift-collection.webp"
                alt=""
                class="h-full w-full object-cover object-right"
            />
            <div
                class="absolute inset-0 bg-linear-to-r from-[#f7f3e8] to-transparent to-25%"
            />
        </div>

        <div
            class="relative mx-auto max-w-7xl px-6 py-12 sm:py-14 lg:px-10 lg:py-16 xl:min-h-160"
        >
            <div class="xl:max-w-xl">
                <RevealOnScroll>
                    <p
                        class="text-xs font-semibold tracking-[0.3em] text-[#b99a45] uppercase md:text-sm"
                    >
                        Why Sevara
                    </p>
                </RevealOnScroll>

                <h2
                    ref="headline"
                    class="mt-3 font-serif text-2xl leading-snug text-[#0f1c3f] sm:mt-4 sm:text-3xl lg:text-4xl"
                >
                    Premium taste, elegant packaging and reliable B2B support
                    for modern trade partners.
                </h2>

                <RevealOnScroll :delay="150">
                    <div class="mt-6 flex items-center gap-4">
                        <span class="h-px w-12 bg-[#d4af37]/40 lg:w-32" />
                        <Diamond class="size-2.5 text-[#d4af37]" />
                        <span class="h-px bg-[#d4af37]/40 lg:w-32" />
                    </div>

                    <div
                        class="mt-6 grid grid-cols-2 gap-2.5 sm:mt-8 sm:grid-cols-3 sm:gap-4"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="group flex flex-col items-center gap-2 rounded-sm border border-[#e7e1d4] bg-white/25 px-3 py-5 text-center transition duration-300 hover:-translate-y-0.5 hover:border-[#d4af37]/60 hover:shadow-[0_14px_34px_-18px_rgba(15,28,63,0.35)] sm:gap-3 sm:px-4 sm:py-6"
                        >
                            <span
                                class="flex size-10 items-center justify-center text-[#b99a45] transition duration-300 group-hover:border-[#d4af37]/70 sm:size-12"
                            >
                                <component
                                    :is="feature.icon"
                                    class="size-7 sm:size-8"
                                    :stroke-width="1.4"
                                />
                            </span>
                            <p
                                class="max-w-28 font-serif text-xs leading-snug text-[#0f1c3f] sm:text-sm"
                            >
                                {{ feature.title }}
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="exportEnquiryHref"
                        class="mt-8 inline-flex items-center gap-2 bg-[#0f1c3f] px-6 py-3 text-xs font-semibold tracking-[0.15em] text-white uppercase transition hover:bg-[#0f1c3f]/90"
                    >
                        Contact Export Team
                        <ArrowRight class="size-4" />
                    </Link>
                </RevealOnScroll>
            </div>
        </div>

        <RevealOnScroll class="xl:hidden">
            <div class="relative mx-auto max-w-2xl px-6 pb-14 lg:px-10">
                <LazyImage
                    src="/images/gift-collection.webp"
                    alt="Sevara Premium Date Collection gift box"
                    class="w-full object-contain"
                />
            </div>
        </RevealOnScroll>
    </section>
</template>
