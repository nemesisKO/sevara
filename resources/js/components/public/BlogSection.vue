<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { index as blogIndex, show as showBlog } from '@/routes/blog';
import type { Blog } from '@/types';

const props = defineProps<{
    blogs: Blog[];
}>();

const featured = props.blogs[0];
const rest = props.blogs.slice(1, 4);
</script>

<template>
    <section v-if="blogs.length > 0" class="bg-[#fdfbf6] py-14 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <div
                class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-[#d4af37] sm:text-sm"
                    >
                        BLOG &amp; INSIGHT
                    </p>
                    <h2
                        class="mt-3 font-serif text-3xl text-[#0f1c3f] sm:text-4xl"
                    >
                        Blog &amp; Insight
                    </h2>
                    <p
                        class="mt-3 max-w-lg text-sm text-[#3a3a35] sm:mt-4 sm:text-base"
                    >
                        Market notes, product ideas and export insights for
                        modern FMCG partners.
                    </p>
                </div>

                <Link
                    :href="blogIndex()"
                    class="inline-flex w-fit shrink-0 items-center gap-2 border border-[#0f1c3f]/30 px-6 py-3 text-xs font-semibold tracking-[0.15em] text-[#0f1c3f] uppercase transition hover:border-[#0f1c3f] hover:bg-[#0f1c3f] hover:text-white"
                >
                    More articles
                </Link>
            </div>

            <div
                class="mt-8 grid grid-cols-2 gap-3 sm:mt-12 sm:gap-6 lg:grid-cols-[1.4fr_1fr_1fr_1fr]"
            >
                <RevealOnScroll>
                    <Link
                        :href="showBlog({ slug: featured.slug })"
                        class="group flex h-full flex-col overflow-hidden rounded-lg border border-[#e7e1d4] bg-white transition-shadow duration-300 hover:shadow-lg"
                    >
                        <div
                            class="relative aspect-4/3 overflow-hidden bg-[#eceae3]"
                        >
                            <LazyImage
                                v-if="featured.image_url"
                                :src="featured.image_url"
                                :alt="featured.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <span
                                class="absolute bottom-3 left-3 bg-[#f7f3e8] px-3 py-1 text-[10px] font-semibold tracking-[0.2em] text-[#0f1c3f] uppercase"
                            >
                                Featured
                            </span>
                        </div>
                        <div class="flex flex-1 flex-col p-3 sm:p-4 lg:p-5">
                            <h3
                                class="font-serif text-sm leading-snug text-[#0f1c3f] transition-colors group-hover:text-[#b99a45] sm:text-base lg:text-xl"
                            >
                                {{ featured.title }}
                            </h3>
                            <p
                                v-if="featured.mini_review"
                                class="mt-2 line-clamp-2 hidden text-sm text-[#6b6558] lg:block"
                            >
                                {{ featured.mini_review }}
                            </p>
                            <span
                                class="mt-auto inline-flex items-center gap-1 pt-3 text-[11px] font-semibold tracking-wide text-[#0f1c3f] sm:text-xs lg:pt-4"
                            >
                                Read article
                                <ArrowRight
                                    class="size-3.5 transition-transform group-hover:translate-x-1"
                                />
                            </span>
                        </div>
                    </Link>
                </RevealOnScroll>

                <RevealOnScroll
                    v-for="(blog, index) in rest"
                    :key="blog.id"
                    :delay="(index + 1) * 100"
                >
                    <Link
                        :href="showBlog({ slug: blog.slug })"
                        class="group flex h-full flex-col overflow-hidden rounded-lg border border-[#e7e1d4] bg-white transition-shadow duration-300 hover:shadow-lg"
                    >
                        <div class="aspect-4/3 overflow-hidden bg-[#eceae3]">
                            <LazyImage
                                v-if="blog.image_url"
                                :src="blog.image_url"
                                :alt="blog.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>
                        <div class="flex flex-1 flex-col p-3 sm:p-4">
                            <p
                                v-if="blog.category"
                                class="hidden text-[11px] font-semibold tracking-[0.15em] text-[#b99a45] uppercase sm:block"
                            >
                                {{ blog.category.name }}
                            </p>
                            <h3
                                class="font-serif text-sm leading-snug text-[#0f1c3f] transition-colors group-hover:text-[#b99a45] sm:mt-1 sm:text-base"
                            >
                                {{ blog.title }}
                            </h3>
                            <span
                                class="mt-auto inline-flex items-center gap-1 pt-3 text-[11px] font-semibold tracking-wide text-[#0f1c3f] sm:text-xs"
                            >
                                Read more
                                <ArrowRight
                                    class="size-3.5 transition-transform group-hover:translate-x-1"
                                />
                            </span>
                        </div>
                    </Link>
                </RevealOnScroll>
            </div>
        </div>
    </section>
</template>
