<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, ChevronLeft, ChevronRight } from '@lucide/vue';
import { useTemplateRef } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { brandNetwork } from '@/routes';
import type { Brand } from '@/types';

defineProps<{
    brands: Brand[];
}>();

const ornament =
    "url('/images/pattern-ornament.webp') center / cover no-repeat";

const fadeFromTopLeft =
    'radial-gradient(circle at bottom left, #000 0%, transparent 90%)';
const fadeFromTopRight =
    'radial-gradient(circle at top right, #000 0%, transparent 90%)';

const track = useTemplateRef('track');

/**
 * The card width varies with the breakpoint, so measure it instead of
 * scrolling by a fixed amount.
 */
function cardWidth(): number {
    const first = track.value?.firstElementChild;

    return first instanceof HTMLElement ? first.offsetWidth + 16 : 176;
}

function scrollByCards(direction: number): void {
    track.value?.scrollBy({
        left: direction * cardWidth(),
        behavior: 'smooth',
    });
}
</script>

<template>
    <section
        v-if="brands.length > 0"
        class="relative overflow-hidden bg-[#fdfbf6] py-8 sm:py-20"
    >
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -bottom-6 -left-6 size-48 opacity-100 sm:size-72 lg:size-112"
                :style="{
                    background: ornament,
                    maskImage: fadeFromTopLeft,
                    WebkitMaskImage: fadeFromTopLeft,
                }"
            />
            <div
                class="absolute -top-6 -right-6 size-48 opacity-100 sm:size-72 lg:size-112"
                :style="{
                    background: ornament,
                    maskImage: fadeFromTopRight,
                    WebkitMaskImage: fadeFromTopRight,
                }"
            />
        </div>

        <div class="relative mx-auto max-w-6xl px-6 text-center lg:px-10">
            <RevealOnScroll>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-primeGold sm:text-sm lg:text-base"
                >
                    OUR BRANDS
                </p>
                <h2
                    class="mt-3 font-serif text-3xl text-primeBlue sm:text-4xl lg:text-5xl"
                >
                    Our Brand Network
                </h2>
                <p
                    class="mx-auto mt-3 max-w-md text-sm text-primeBlue sm:mt-4 md:text-lg"
                >
                    Built on food industry experience, trade relationships and
                    modern FMCG ambition.
                </p>
            </RevealOnScroll>

            <div class="relative mt-8 sm:mt-12">
                <button
                    v-if="brands.length > 5"
                    type="button"
                    aria-label="Scroll left"
                    class="absolute top-1/2 left-0 z-10 hidden size-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-[#e7e1d4] bg-[#fdfbf6] text-[#0f1c3f] transition hover:bg-white sm:flex"
                    @click="scrollByCards(-1)"
                >
                    <ChevronLeft class="size-5" />
                </button>

                <div
                    ref="track"
                    class="flex snap-x snap-mandatory [scrollbar-width:none] gap-4 overflow-x-auto scroll-smooth px-1"
                >
                    <RevealOnScroll
                        v-for="(brand, index) in brands"
                        :key="brand.id"
                        :delay="index * 120"
                        class="shrink-0 basis-[calc((100%-1rem)/2)] snap-start sm:basis-[calc((100%-2rem)/3)] lg:basis-[calc((100%-4rem)/5)]"
                    >
                        <a
                            :href="brand.link"
                            class="group relative flex aspect-square w-full items-center justify-center overflow-hidden rounded-lg border border-[#e7e1d4] bg-white p-4 transition duration-300 ease-out hover:-translate-y-1.5 hover:border-[#d4af37]/70 hover:shadow-[0_20px_45px_-24px_rgba(15,28,63,0.5)] motion-reduce:hover:translate-y-0 sm:p-6"
                        >
                            <span
                                class="pointer-events-none absolute inset-0 bg-linear-to-br from-[#d4af37]/0 via-[#d4af37]/0 to-[#d4af37]/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            />
                            <LazyImage
                                :src="brand.image_url"
                                :alt="brand.name"
                                class="relative max-h-full max-w-full object-contain opacity-80 grayscale transition duration-500 ease-out group-hover:scale-105 group-hover:opacity-100 group-hover:grayscale-0"
                            />
                        </a>
                    </RevealOnScroll>
                </div>

                <button
                    v-if="brands.length > 5"
                    type="button"
                    aria-label="Scroll right"
                    class="absolute top-1/2 right-0 z-10 hidden size-10 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-[#e7e1d4] bg-[#fdfbf6] text-[#0f1c3f] transition hover:bg-white sm:flex"
                    @click="scrollByCards(1)"
                >
                    <ChevronRight class="size-5" />
                </button>
            </div>

            <RevealOnScroll :delay="150">
                <Link
                    :href="brandNetwork()"
                    class="group mt-8 inline-flex items-center gap-2 rounded-md bg-primeGold px-5 py-2.5 text-xs font-semibold tracking-wide text-white transition hover:bg-[#c9a227] sm:mt-10 sm:px-6 sm:py-3 sm:text-sm"
                >
                    VIEW BRAND NETWORK
                    <ArrowRight
                        class="size-4 transition-transform duration-300 group-hover:translate-x-1"
                    />
                </Link>
            </RevealOnScroll>
        </div>
    </section>
</template>
