<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import type { Slide } from '@/types';

const { slides } = defineProps<{
    slides: Slide[];
}>();

const activeIndex = ref(0);

/** Must match the opacity transition duration on the slide anchors. */
const fadeDuration = 1500;

/**
 * The outgoing slide stays fully opaque beneath the incoming one, so the two
 * images dissolve into each other instead of both fading through the
 * background and dipping to dark mid-transition.
 */
const previousIndex = ref<number | null>(null);

let autoplayTimer: ReturnType<typeof setInterval> | null = null;
let releasePreviousTimer: ReturnType<typeof setTimeout> | null = null;

function goTo(index: number): void {
    const target = (index + slides.length) % slides.length;

    if (target === activeIndex.value) {
        return;
    }

    if (releasePreviousTimer) {
        clearTimeout(releasePreviousTimer);
    }

    previousIndex.value = activeIndex.value;
    activeIndex.value = target;

    /**
     * Once the incoming slide covers it, drop the outgoing one back to
     * transparent. Without this it stays opaque forever and the next visit to
     * that slide has nothing left to fade, so it would cut in instantly.
     */
    releasePreviousTimer = setTimeout(() => {
        previousIndex.value = null;
        releasePreviousTimer = null;
    }, fadeDuration);
}

function next(): void {
    goTo(activeIndex.value + 1);
}

function previous(): void {
    goTo(activeIndex.value - 1);
}

function startAutoplay(): void {
    if (slides.length <= 1) {
        return;
    }

    autoplayTimer = setInterval(next, 6000);
}

function stopAutoplay(): void {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

onMounted(startAutoplay);

onBeforeUnmount(() => {
    stopAutoplay();

    if (releasePreviousTimer) {
        clearTimeout(releasePreviousTimer);
        releasePreviousTimer = null;
    }
});
</script>

<template>
    <section
        v-if="slides.length > 0"
        class="relative aspect-[16/9] w-full overflow-hidden bg-[#0f1c3f] sm:aspect-[21/9]"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
    >
        <a
            v-for="(slide, index) in slides"
            :key="slide.id"
            :href="slide.link"
            class="absolute inset-0 transition-opacity duration-1500 ease-in-out motion-reduce:transition-none"
            :class="[
                index === activeIndex
                    ? 'z-20 opacity-100'
                    : 'pointer-events-none',
                index === previousIndex
                    ? 'z-10 opacity-100'
                    : index !== activeIndex && 'z-0 opacity-0',
            ]"
        >
            <img
                :src="slide.image_url"
                alt=""
                class="h-full w-full object-cover transition-transform duration-2400 ease-out motion-reduce:transition-none"
                :class="index === activeIndex ? 'scale-100' : 'scale-105'"
            />
        </a>

        <template v-if="slides.length > 1">
            <button
                type="button"
                aria-label="Previous slide"
                class="absolute top-1/2 left-2 z-30 flex size-10 -translate-y-1/2 items-center justify-center text-white/80 drop-shadow-md transition hover:scale-110 hover:text-white sm:left-4"
                @click="previous"
            >
                <ChevronLeft class="size-6 sm:size-8" stroke-width="1.5" />
            </button>
            <button
                type="button"
                aria-label="Next slide"
                class="absolute top-1/2 right-2 z-30 flex size-10 -translate-y-1/2 items-center justify-center text-white/80 drop-shadow-md transition hover:scale-110 hover:text-white sm:right-4"
                @click="next"
            >
                <ChevronRight class="size-6 sm:size-8" stroke-width="1.5" />
            </button>

            <div
                class="absolute bottom-3 left-1/2 z-30 flex -translate-x-1/2 gap-2 sm:bottom-5"
            >
                <button
                    v-for="(slide, index) in slides"
                    :key="slide.id"
                    type="button"
                    :aria-label="`Go to slide ${index + 1}`"
                    class="size-2 rounded-full bg-[#d4af37] transition sm:size-2.5"
                    :class="
                        index === activeIndex
                            ? 'opacity-100'
                            : 'opacity-40 hover:opacity-70'
                    "
                    @click="goTo(index)"
                />
            </div>
        </template>
    </section>
</template>
