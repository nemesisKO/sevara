<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import { contact } from '@/routes';
import { show as showProduct } from '@/routes/products';
import type { Product } from '@/types';

defineProps<{
    products: Product[];
}>();

const ornament =
    "url('/images/pattern-ornament.webp') center / cover no-repeat";
const fadeFromTopLeft =
    'radial-gradient(circle at top left, #000 0%, transparent 70%)';

const track = useTemplateRef('track');
const activeIndex = ref(0);

function cardWidth(): number {
    const first = track.value?.firstElementChild;

    return first instanceof HTMLElement ? first.offsetWidth + 24 : 320;
}

function scrollByCards(direction: number): void {
    track.value?.scrollBy({
        left: direction * cardWidth(),
        behavior: 'smooth',
    });
}

function syncActiveIndex(): void {
    if (!track.value) {
        return;
    }

    activeIndex.value = Math.round(track.value.scrollLeft / cardWidth());
}

function goTo(index: number): void {
    track.value?.scrollTo({ left: index * cardWidth(), behavior: 'smooth' });
}

onMounted(() => {
    track.value?.addEventListener('scroll', syncActiveIndex, { passive: true });
});

onBeforeUnmount(() => {
    track.value?.removeEventListener('scroll', syncActiveIndex);
});
</script>

<template>
    <section
        v-if="products.length > 0"
        class="relative overflow-hidden bg-[#faf7f0] py-12 sm:py-16"
    >
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-6 -left-6 size-48 opacity-60 sm:size-72 lg:size-112"
                :style="{
                    background: ornament,
                    maskImage: fadeFromTopLeft,
                    WebkitMaskImage: fadeFromTopLeft,
                }"
            />
        </div>

        <div class="relative mx-auto max-w-3xl px-6 text-center">
            <p
                class="text-xs font-semibold tracking-[0.3em] text-[#b99a45] uppercase md:text-sm"
            >
                Chocolate Date Collection
            </p>
            <h2 class="mt-3 font-serif text-3xl text-[#0f1c3f] sm:text-4xl">
                Chocolate Date Collection
            </h2>
            <p class="mt-3 text-sm text-[#3a3a35] sm:mt-4 sm:text-base">
                Premium flavor lines designed for distributors and wholesale
                partners.
            </p>
        </div>

        <div class="relative mt-8 sm:mt-10">
            <button
                type="button"
                aria-label="Previous products"
                class="absolute top-1/2 left-6 z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full border border-[#e0d7c3] bg-white/70 text-[#0f1c3f] transition hover:border-[#d4af37] hover:bg-white lg:flex xl:left-14"
                @click="scrollByCards(-1)"
            >
                <ChevronLeft class="size-5" />
            </button>

            <div
                ref="track"
                class="flex snap-x snap-mandatory [scrollbar-width:none] gap-6 overflow-x-auto scroll-smooth px-6 pb-2 lg:px-24 xl:px-40"
            >
                <article
                    v-for="product in products"
                    :key="product.id"
                    class="flex w-64 shrink-0 snap-start flex-col overflow-hidden rounded-sm border border-[#e7e1d4] bg-white/70 transition duration-300 hover:border-[#d4af37]/60 hover:shadow-[0_18px_40px_-24px_rgba(15,28,63,0.4)] sm:w-72"
                >
                    <Link
                        :href="showProduct({ product: product.slug })"
                        class="group block aspect-5/4 overflow-hidden bg-[#eceae3]"
                    >
                        <LazyImage
                            v-if="product.images[0]"
                            :src="product.images[0].image_url"
                            :alt="product.title_part1"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </Link>

                    <div
                        class="flex flex-1 flex-col items-center px-5 py-5 text-center sm:px-6 sm:py-6"
                    >
                        <h3
                            class="font-serif text-xl text-[#0f1c3f] sm:text-2xl"
                        >
                            {{ product.title_part1 }}
                        </h3>
                        <p
                            v-if="product.subtitle"
                            class="mt-2 pb-5 text-sm leading-relaxed text-[#6b6558] sm:pb-6"
                        >
                            {{ product.subtitle }}
                        </p>

                        <Link
                            :href="contact()"
                            class="mt-auto w-full bg-[#0f1c3f] px-6 py-3 text-xs font-semibold tracking-[0.15em] text-white uppercase transition hover:bg-[#0f1c3f]/90"
                        >
                            Request Sample
                        </Link>
                    </div>
                </article>
            </div>

            <button
                type="button"
                aria-label="Next products"
                class="absolute top-1/2 right-6 z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full border border-[#e0d7c3] bg-white/70 text-[#0f1c3f] transition hover:border-[#d4af37] hover:bg-white lg:flex xl:right-14"
                @click="scrollByCards(1)"
            >
                <ChevronRight class="size-5" />
            </button>
        </div>

        <div
            v-if="products.length > 1"
            class="relative mt-8 flex items-center justify-center gap-3"
        >
            <button
                v-for="(product, index) in products"
                :key="product.id"
                type="button"
                :aria-label="`Go to ${product.title_part1}`"
                class="size-2.5 rounded-full border border-[#d4af37] transition"
                :class="
                    index === activeIndex
                        ? 'bg-[#d4af37]'
                        : 'bg-transparent opacity-50 hover:opacity-100'
                "
                @click="goTo(index)"
            />
        </div>
    </section>
</template>
