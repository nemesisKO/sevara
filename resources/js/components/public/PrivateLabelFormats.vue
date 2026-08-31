<script setup lang="ts">
import { ChevronLeft, ChevronRight, Package } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import type { PackagingFormat } from '@/types';

const { featuredFormats, otherFormats } = defineProps<{
    featuredFormats: PackagingFormat[];
    otherFormats: PackagingFormat[];
}>();

const totalFormats = computed(
    () => featuredFormats.length + otherFormats.length,
);

const formats = computed(() => [...featuredFormats, ...otherFormats]);

const track = useTemplateRef('track');
const pageCount = ref(1);
const activePage = ref(0);

let resizeObserver: ResizeObserver | null = null;

function syncPages(): void {
    const el = track.value;

    if (!el || el.clientWidth === 0) {
        return;
    }

    pageCount.value = Math.max(
        1,
        Math.ceil((el.scrollWidth - 1) / el.clientWidth),
    );
    activePage.value = Math.min(
        Math.round(el.scrollLeft / el.clientWidth),
        pageCount.value - 1,
    );
}

function goToPage(page: number): void {
    const el = track.value;

    if (!el) {
        return;
    }

    el.scrollTo({
        left: Math.min(Math.max(page, 0), pageCount.value - 1) * el.clientWidth,
        behavior: 'smooth',
    });
}

function step(direction: number): void {
    goToPage(activePage.value + direction);
}

onMounted(() => {
    syncPages();

    if (track.value) {
        resizeObserver = new ResizeObserver(syncPages);
        resizeObserver.observe(track.value);
    }
});

onBeforeUnmount(() => {
    resizeObserver?.disconnect();
});
</script>

<template>
    <section
        v-if="totalFormats > 0"
        class="bg-[#f7f3e8]/60 py-20"
        aria-labelledby="packaging-formats-heading"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <RevealOnScroll>
                <div class="text-center">
                    <h2
                        id="packaging-formats-heading"
                        class="font-serif text-3xl text-primeBlue sm:text-4xl"
                    >
                        Choose Your Packaging Format
                    </h2>
                    <div
                        class="mt-4 flex flex-col items-center justify-center gap-3 sm:flex-row"
                    >
                        <p class="text-primeBlue">
                            Flexible formats for retail, gifting, hospitality
                            and distribution.
                        </p>
                        <span
                            class="rounded-full border border-primeGold/50 px-4 py-1 text-xs font-semibold tracking-[0.15em] text-primeGold uppercase"
                        >
                            {{ totalFormats }} format options
                        </span>
                    </div>
                </div>
            </RevealOnScroll>

            <div
                v-if="formats.length > 0"
                class="mt-12 flex items-center gap-3 lg:gap-5"
            >
                <button
                    type="button"
                    aria-label="Previous formats"
                    class="hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-primeGold/50 text-primeGold transition hover:bg-[#d4af37]/10 disabled:cursor-default disabled:opacity-30 sm:flex"
                    :disabled="activePage === 0"
                    @click="step(-1)"
                >
                    <ChevronLeft class="size-5" />
                </button>

                <div
                    ref="track"
                    class="-mx-2 flex flex-1 snap-x snap-mandatory [scrollbar-width:none] overflow-x-auto scroll-smooth [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
                    @scroll.passive="syncPages"
                >
                    <div
                        v-for="format in formats"
                        :key="format.id"
                        class="w-full shrink-0 snap-start px-2 sm:w-1/2 lg:w-1/5"
                    >
                        <article
                            class="flex h-full flex-col rounded-lg border border-[#e7e1d4] bg-white p-5 text-center transition duration-300 hover:-translate-y-1 hover:border-[#d4af37]/60"
                        >
                            <div
                                class="flex aspect-square items-center justify-center overflow-hidden rounded-md bg-[#f7f3e8]/70"
                            >
                                <LazyImage
                                    v-if="format.image_url"
                                    :src="format.image_url"
                                    :alt="format.name"
                                    class="h-full w-full object-contain mix-blend-multiply"
                                />
                                <Package
                                    v-else
                                    class="size-12 text-[#d4af37]/40"
                                    :stroke-width="1"
                                />
                            </div>

                            <h3
                                class="mt-5 font-serif text-lg text-primeBlue sm:text-xl"
                            >
                                {{ format.name }}
                            </h3>
                            <p
                                v-if="format.description"
                                class="mt-2 text-sm text-primeBlue/90"
                            >
                                {{ format.description }}
                            </p>
                        </article>
                    </div>
                </div>

                <button
                    type="button"
                    aria-label="Next formats"
                    class="hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-[#d4af37]/50 text-[#b99a45] transition hover:bg-[#d4af37]/10 disabled:cursor-default disabled:opacity-30 sm:flex"
                    :disabled="activePage >= pageCount - 1"
                    @click="step(1)"
                >
                    <ChevronRight class="size-5" />
                </button>
            </div>

            <div
                v-if="pageCount > 1"
                class="mt-10 flex items-center justify-center gap-2.5"
            >
                <button
                    v-for="page in pageCount"
                    :key="page"
                    type="button"
                    :aria-label="`Go to format page ${page}`"
                    class="size-2.5 cursor-pointer rounded-full bg-[#d4af37] transition"
                    :class="
                        page - 1 === activePage
                            ? 'opacity-100'
                            : 'opacity-30 hover:opacity-60'
                    "
                    @click="goToPage(page - 1)"
                />
            </div>
        </div>
    </section>
</template>
