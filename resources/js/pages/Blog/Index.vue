<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { show as showBlog } from '@/routes/blog';
import type { Blog, Paginated } from '@/types';

defineProps<{
    blogs: Paginated<Blog>;
}>();
</script>

<template>
    <Head title="Blog & Insight" />

    <section class="border-b border-[#e7e1d4] bg-[#fdfbf6] py-16">
        <div class="mx-auto max-w-6xl px-6 text-center lg:px-10">
            <p class="text-sm font-semibold tracking-[0.2em] text-[#d4af37]">
                BLOG &amp; INSIGHT
            </p>
            <h1 class="mt-3 font-serif text-4xl text-[#0f1c3f] sm:text-5xl">
                Blog &amp; Insight
            </h1>
            <p class="mx-auto mt-4 max-w-xl text-[#3a3a35]">
                Market notes, product ideas and export insights for modern FMCG
                partners.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16 lg:px-10">
        <div
            v-if="blogs.data.length === 0"
            class="py-24 text-center text-[#6b6558]"
        >
            No posts published yet. Check back soon.
        </div>

        <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <RevealOnScroll
                v-for="(blog, index) in blogs.data"
                :key="blog.id"
                :delay="(index % 3) * 100"
            >
                <Link
                    :href="showBlog({ slug: blog.slug })"
                    class="group flex h-full flex-col overflow-hidden rounded-lg border border-[#e7e1d4] bg-white transition-shadow duration-300 hover:shadow-lg"
                >
                    <div class="aspect-video overflow-hidden bg-[#eceae3]">
                        <LazyImage
                            v-if="blog.image_url"
                            :src="blog.image_url"
                            :alt="blog.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <p
                            v-if="blog.category"
                            class="text-xs font-semibold tracking-[0.15em] text-[#b99a45] uppercase"
                        >
                            {{ blog.category.name }}
                        </p>
                        <h2
                            class="mt-2 font-serif text-lg text-[#0f1c3f] transition-colors group-hover:text-[#b99a45]"
                        >
                            {{ blog.title }}
                        </h2>
                        <p
                            v-if="blog.mini_review"
                            class="mt-2 line-clamp-2 text-sm text-[#6b6558]"
                        >
                            {{ blog.mini_review }}
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-1 text-xs font-semibold tracking-wide text-[#0f1c3f]"
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

        <nav
            v-if="blogs.last_page > 1"
            class="mt-14 flex flex-wrap items-center justify-center gap-2"
        >
            <template v-for="(link, index) in blogs.links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="inline-flex min-w-9 items-center justify-center rounded-md px-3 py-2 text-sm transition-colors"
                    :class="
                        link.active
                            ? 'bg-[#0f1c3f] text-white'
                            : 'text-[#3a3a35] hover:bg-[#f7f3e8]'
                    "
                >
                    <span v-html="link.label" />
                </Link>
                <span
                    v-else
                    class="inline-flex min-w-9 items-center justify-center px-3 py-2 text-sm text-[#a89f8d]"
                    v-html="link.label"
                />
            </template>
        </nav>
    </section>
</template>
