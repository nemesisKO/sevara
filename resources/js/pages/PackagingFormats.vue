<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Package } from '@lucide/vue';
import { computed } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import { privateLabel } from '@/routes';
import type { PackagingFormat } from '@/types';

const { featuredFormats, otherFormats } = defineProps<{
    featuredFormats: PackagingFormat[];
    otherFormats: PackagingFormat[];
}>();

const formats = computed(() => [...featuredFormats, ...otherFormats]);
</script>

<template>
    <div>
        <Head title="Packaging Formats" />

        <section
            class="bg-[#f7f3e8]/60 py-16 lg:py-20"
            aria-labelledby="packaging-formats-heading"
        >
            <div class="mx-auto max-w-7xl px-6 lg:px-10">
                <Link
                    :href="privateLabel()"
                    class="text-sm font-medium text-primeGold transition hover:text-primeBlue"
                >
                    &larr; Back to Private Label
                </Link>

                <div class="mt-10 text-center">
                    <h1
                        id="packaging-formats-heading"
                        class="font-serif text-3xl text-primeBlue sm:text-4xl"
                    >
                        Choose Your Packaging Format
                    </h1>
                    <p class="mt-4 text-primeBlue">
                        Flexible formats for retail, gifting, hospitality and
                        distribution.
                    </p>
                </div>

                <div
                    v-if="formats.length > 0"
                    class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <article
                        v-for="format in formats"
                        :key="format.id"
                        class="flex h-full flex-col rounded-lg border border-[#e7e1d4] bg-white p-5 text-center"
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
                        <h2
                            class="mt-5 font-serif text-lg text-primeBlue sm:text-xl"
                        >
                            {{ format.name }}
                        </h2>
                        <p
                            v-if="format.description"
                            class="mt-2 text-sm text-primeBlue/90"
                        >
                            {{ format.description }}
                        </p>
                    </article>
                </div>

                <p v-else class="mt-12 text-center text-sm text-primeBlue/70">
                    Packaging formats are coming soon.
                </p>
            </div>
        </section>
    </div>
</template>
