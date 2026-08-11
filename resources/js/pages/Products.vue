<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Diamond } from '@lucide/vue';
import LazyImage from '@/components/LazyImage.vue';
import PhotoPlaceholder from '@/components/public/PhotoPlaceholder.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { contact } from '@/routes';
import { request as requestCatalog } from '@/routes/catalog';
import { show as showProduct } from '@/routes/products';
import type { Product } from '@/types';

defineProps<{
    products: Product[];
}>();

const mandala = "url('/images/mandala.png') center / contain no-repeat";
</script>

<template>
    <Head title="Chocolate Date Collection" />

    <section class="relative overflow-hidden border-b border-[#e7e1d4]">
        <div
            class="pointer-events-none absolute -top-20 -right-32 size-120 rotate-12 opacity-[0.06] grayscale"
            :style="{ background: mandala }"
        />

        <div
            class="relative mx-auto max-w-3xl px-6 py-20 text-center lg:px-10 lg:py-24"
        >
            <p
                class="text-xs font-semibold tracking-[0.3em] text-[#b99a45] uppercase"
            >
                Our Products
            </p>
            <h1
                class="mt-4 font-serif text-4xl leading-tight text-[#0f1c3f] sm:text-5xl"
            >
                Chocolate Date Collection
            </h1>

            <div class="mt-7 flex items-center justify-center gap-4">
                <span class="h-px w-16 bg-[#d4af37]/50" />
                <Diamond class="size-3 text-[#d4af37]" />
                <span class="h-px w-16 bg-[#d4af37]/50" />
            </div>

            <p class="mx-auto mt-7 max-w-xl text-[#6b6558]">
                Premium flavor lines designed for distributors, retail chains
                and wholesale partners.
            </p>
        </div>
    </section>

    <section class="bg-[#faf7f0] py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <div
                v-if="products.length > 0"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <RevealOnScroll
                    v-for="(product, index) in products"
                    :key="product.id"
                    :delay="(index % 4) * 100"
                >
                    <article
                        class="flex h-full flex-col overflow-hidden rounded-sm border border-[#e7e1d4] bg-white/70 transition duration-300 hover:border-[#d4af37]/60 hover:shadow-[0_18px_40px_-24px_rgba(15,28,63,0.4)]"
                    >
                        <Link
                            :href="showProduct({ product: product.slug })"
                            class="group block aspect-5/4 overflow-hidden bg-[#eceae3]"
                        >
                            <LazyImage
                                v-if="product.images[0]"
                                :src="product.images[0].image_url"
                                :alt="product.seo_title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <PhotoPlaceholder v-else />
                        </Link>

                        <div
                            class="flex flex-1 flex-col items-center px-6 py-6 text-center"
                        >
                            <h2 class="font-serif text-2xl text-[#0f1c3f]">
                                {{ product.title_part1 }}
                            </h2>
                            <p
                                v-if="product.subtitle"
                                class="mt-2 pb-6 text-sm leading-relaxed text-[#6b6558]"
                            >
                                {{ product.subtitle }}
                            </p>

                            <Link
                                :href="showProduct({ product: product.slug })"
                                class="mt-auto w-full bg-[#0f1c3f] px-6 py-3 text-xs font-semibold tracking-[0.15em] text-white uppercase transition hover:bg-[#0f1c3f]/90"
                            >
                                View Product
                            </Link>
                        </div>
                    </article>
                </RevealOnScroll>
            </div>

            <p v-else class="py-10 text-center text-[#6b6558]">
                Our collection is coming soon.
            </p>
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
                Looking for wholesale pricing or samples?
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
                    :href="contact()"
                    class="inline-flex items-center gap-2 border border-[#d4af37]/60 px-6 py-3.5 text-xs font-semibold tracking-[0.15em] text-[#e9d9ae] uppercase transition hover:bg-[#d4af37]/10"
                >
                    Contact Export Team
                </Link>
            </div>
        </div>
    </section>
</template>
