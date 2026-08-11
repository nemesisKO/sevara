<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LazyImage from '@/components/LazyImage.vue';
import type { Brand } from '@/types';

defineProps<{
    brands: Brand[];
}>();
</script>

<template>
    <Head title="Brand Network" />

    <div class="mx-auto max-w-6xl px-6 py-20 text-center lg:px-10">
        <p class="text-sm font-semibold tracking-[0.2em] text-[#d4af37]">
            OUR BRANDS
        </p>
        <h1 class="mt-3 font-serif text-4xl text-[#0f1c3f]">
            Our Brand Network
        </h1>
        <p class="mx-auto mt-4 max-w-xl text-[#3a3a35]">
            Built on food industry experience, trade relationships and modern
            FMCG ambition.
        </p>

        <div
            v-if="brands.length === 0"
            class="mt-16 text-sm text-muted-foreground"
        >
            No brands to show yet.
        </div>

        <div
            v-else
            class="mt-16 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"
        >
            <a
                v-for="brand in brands"
                :key="brand.id"
                :href="brand.link"
                class="flex aspect-square items-center justify-center rounded-lg border border-[#e7e1d4] bg-white p-6 transition hover:border-[#d4af37]"
            >
                <LazyImage
                    :src="brand.image_url"
                    :alt="brand.name"
                    class="max-h-full max-w-full object-contain"
                />
            </a>
        </div>
    </div>
</template>
