<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Calendar,
    ChevronDown,
    Clock,
    Diamond,
    User,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import JsonLd from '@/components/JsonLd.vue';
import LazyImage from '@/components/LazyImage.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import { index as blogIndex, show as showBlog } from '@/routes/blog';
import type { Blog } from '@/types';

const props = defineProps<{
    blog: Blog;
    recentBlogs: Blog[];
}>();

const page = usePage();

const seo = computed(() => page.props.seo);

const articleUrl = computed(() => `${seo.value.url}/blog/${props.blog.slug}`);

const metaDescription = computed(
    () =>
        props.blog.meta_description ??
        props.blog.mini_review ??
        seo.value.description,
);

const structuredData = computed(() => {
    const graph: Record<string, unknown>[] = [
        {
            '@type': 'BlogPosting',
            '@id': `${articleUrl.value}#article`,
            headline: props.blog.title,
            description: metaDescription.value,
            url: articleUrl.value,
            mainEntityOfPage: articleUrl.value,
            /**
             * created_at and updated_at are used rather than the display-only
             * custom_date, which is free text and not guaranteed ISO 8601.
             */
            datePublished: props.blog.created_at,
            dateModified: props.blog.updated_at,
            ...(props.blog.image_url
                ? { image: `${seo.value.url}${props.blog.image_url}` }
                : {}),
            author: {
                '@type': props.blog.author ? 'Person' : 'Organization',
                name: props.blog.author ?? seo.value.name,
            },
            publisher: { '@id': `${seo.value.url}/#organization` },
            ...(props.blog.category
                ? { articleSection: props.blog.category.name }
                : {}),
            ...(props.blog.tags.length > 0
                ? {
                      keywords: props.blog.tags
                          .map((tag) => tag.title)
                          .join(', '),
                  }
                : {}),
            isPartOf: { '@id': `${seo.value.url}/#website` },
            inLanguage: 'en',
        },
    ];

    if (props.blog.faqs && props.blog.faqs.length > 0) {
        graph.push({
            '@type': 'FAQPage',
            '@id': `${articleUrl.value}#faq`,
            mainEntity: props.blog.faqs.map((faq) => ({
                '@type': 'Question',
                name: faq.question,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: faq.answer,
                },
            })),
        });
    }

    return {
        '@context': 'https://schema.org',
        '@graph': graph,
    };
});

const openFaq = ref<number | null>(null);

function toggleFaq(index: number): void {
    openFaq.value = openFaq.value === index ? null : index;
}
</script>

<template>
    <Head :title="blog.title">
        <meta
            head-key="description"
            name="description"
            :content="metaDescription"
        />
        <meta head-key="og:type" property="og:type" content="article" />
        <meta head-key="og:title" property="og:title" :content="blog.title" />
        <meta
            head-key="og:description"
            property="og:description"
            :content="metaDescription"
        />
        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="blog.title"
        />
        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="metaDescription"
        />
    </Head>
    <JsonLd :data="structuredData" head-key="article" />

    <section class="border-b border-[#e7e1d4] bg-[#fdfbf6] py-14">
        <div class="mx-auto max-w-3xl px-6 text-center lg:px-10">
            <Link
                :href="blogIndex()"
                class="text-xs font-semibold tracking-[0.2em] text-[#d4af37] uppercase hover:text-[#b99a45]"
            >
                Blog &amp; Insight
            </Link>

            <p
                v-if="blog.category"
                class="mt-4 inline-flex rounded-full border border-[#d4af37]/40 px-4 py-1 text-xs font-semibold tracking-[0.15em] text-[#0f1c3f] uppercase"
            >
                {{ blog.category.name }}
            </p>

            <h1
                class="mt-5 font-serif text-3xl leading-snug text-[#0f1c3f] sm:text-4xl"
            >
                {{ blog.title }}
            </h1>

            <p
                v-if="blog.mini_review"
                class="mx-auto mt-4 max-w-xl text-[#6b6558]"
            >
                {{ blog.mini_review }}
            </p>

            <div
                class="mt-6 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-[#8a8478]"
            >
                <span
                    v-if="blog.author"
                    class="inline-flex items-center gap-1.5"
                >
                    <User class="size-3.5 text-[#b99a45]" />
                    {{ blog.author }}
                </span>
                <span
                    v-if="blog.custom_date"
                    class="inline-flex items-center gap-1.5"
                >
                    <Calendar class="size-3.5 text-[#b99a45]" />
                    {{ blog.custom_date }}
                </span>
                <span
                    v-if="blog.read_duration"
                    class="inline-flex items-center gap-1.5"
                >
                    <Clock class="size-3.5 text-[#b99a45]" />
                    {{ blog.read_duration }} min read
                </span>
            </div>
        </div>
    </section>

    <LazyImage
        v-if="blog.image_url"
        :src="blog.image_url"
        :alt="blog.title"
        class="mx-auto -mt-1 aspect-21/9 w-full max-w-5xl object-cover px-0 sm:mt-6 sm:rounded-xl sm:px-6"
    />

    <article class="mx-auto max-w-3xl px-6 py-16 lg:px-10">
        <template v-for="block in blog.blocks" :key="block.id">
            <div
                v-if="block.type === 'article' && block.body"
                class="blog-prose mb-8 text-base leading-loose text-[#3a3a35]"
                v-html="block.body"
            />

            <div
                v-else-if="block.type === 'image' && block.image_url"
                class="my-10 overflow-hidden rounded-xl"
            >
                <LazyImage
                    :src="block.image_url"
                    :alt="blog.title"
                    class="w-full object-cover"
                />
            </div>

            <div
                v-else-if="block.type === 'hint'"
                class="relative my-12 rounded-xl border border-[#e7e1d4] bg-[#f7f3e8]/60 px-8 py-10 text-center"
            >
                <span
                    class="absolute -top-5 left-1/2 flex size-10 -translate-x-1/2 items-center justify-center rounded-full bg-[#0f1c3f] text-[#d4af37]"
                >
                    <Diamond class="size-4" />
                </span>
                <p v-if="block.title" class="font-serif text-lg text-[#0f1c3f]">
                    {{ block.title }}
                </p>
                <p v-if="block.body" class="mt-2 text-sm text-[#6b6558]">
                    {{ block.body }}
                </p>
                <a
                    v-if="block.link_url"
                    :href="block.link_url"
                    class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[#0f1c3f] hover:text-[#b99a45]"
                >
                    {{ block.link_text || 'Learn more' }}
                    <ArrowRight class="size-3.5" />
                </a>
            </div>

            <div
                v-else-if="block.type === 'table' && block.table_data"
                class="my-10"
            >
                <h3
                    v-if="block.title"
                    class="mb-4 font-serif text-xl text-[#0f1c3f]"
                >
                    {{ block.title }}
                </h3>
                <div class="overflow-x-auto rounded-lg border border-[#e7e1d4]">
                    <table
                        class="w-full min-w-100 border-collapse text-left text-sm"
                    >
                        <tbody>
                            <tr
                                v-for="(row, rowIndex) in block.table_data"
                                :key="rowIndex"
                                :class="
                                    rowIndex === 0
                                        ? 'bg-[#0f1c3f]'
                                        : 'odd:bg-[#f7f3e8]/40'
                                "
                            >
                                <component
                                    :is="rowIndex === 0 ? 'th' : 'td'"
                                    v-for="(cell, cellIndex) in row"
                                    :key="cellIndex"
                                    class="border-t border-[#e7e1d4] px-5 py-3"
                                    :class="
                                        rowIndex === 0
                                            ? 'border-t-0 font-semibold tracking-wide text-white uppercase'
                                            : 'text-[#3a3a35]'
                                    "
                                >
                                    <a
                                        v-if="cell.link_url"
                                        :href="cell.link_url"
                                        class="underline underline-offset-2 hover:text-[#b99a45]"
                                        :class="
                                            rowIndex === 0 && 'text-[#d4af37]'
                                        "
                                    >
                                        {{ cell.text }}
                                    </a>
                                    <template v-else>{{ cell.text }}</template>
                                </component>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <div
            v-if="blog.tags.length > 0"
            class="mt-12 flex flex-wrap gap-2 border-t border-[#e7e1d4] pt-8"
        >
            <span
                v-for="tag in blog.tags"
                :key="tag.id"
                class="rounded-full bg-[#f7f3e8] px-3 py-1 text-xs text-[#6b6558]"
            >
                #{{ tag.title }}
            </span>
        </div>

        <section v-if="blog.faqs && blog.faqs.length > 0" class="mt-16">
            <RevealOnScroll>
                <h2 class="font-serif text-2xl text-[#0f1c3f]">
                    Frequently Asked Questions
                </h2>
            </RevealOnScroll>
            <div
                class="mt-6 divide-y divide-[#e7e1d4] border-y border-[#e7e1d4]"
            >
                <div v-for="(faq, index) in blog.faqs" :key="index">
                    <button
                        type="button"
                        class="group flex w-full cursor-pointer items-center justify-between gap-5 py-5 text-left"
                        :aria-expanded="openFaq === index"
                        @click="toggleFaq(index)"
                    >
                        <span
                            class="text-sm font-medium text-[#0f1c3f] transition-colors group-hover:text-[#b99a45]"
                        >
                            {{ faq.question }}
                        </span>
                        <ChevronDown
                            class="size-4 shrink-0 text-[#b99a45] transition-transform duration-300"
                            :class="openFaq === index && 'rotate-180'"
                        />
                    </button>
                    <div
                        class="grid transition-[grid-template-rows] duration-300 ease-out"
                        :class="
                            openFaq === index
                                ? 'grid-rows-[1fr]'
                                : 'grid-rows-[0fr]'
                        "
                    >
                        <div class="overflow-hidden">
                            <p class="pb-5 text-sm text-[#6b6558]">
                                {{ faq.answer }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            v-if="blog.references && blog.references.length > 0"
            class="mt-16 border-t border-[#e7e1d4] pt-8"
        >
            <h2 class="font-serif text-xl text-[#0f1c3f]">References</h2>
            <div class="mt-4 grid gap-2" style="direction: ltr">
                <a
                    v-for="(reference, index) in blog.references"
                    :key="index"
                    :href="reference"
                    target="_blank"
                    rel="noopener"
                    class="truncate rounded-lg border border-[#edf2f7] bg-[#f8fafc] px-4 py-3 text-sm text-[#475569] hover:text-[#0f1c3f]"
                >
                    {{ reference }}
                </a>
            </div>
        </section>
    </article>

    <section
        v-if="recentBlogs.length > 0"
        class="border-t border-[#e7e1d4] bg-[#fdfbf6] py-16"
    >
        <div class="mx-auto max-w-6xl px-6 lg:px-10">
            <h2 class="text-center font-serif text-2xl text-[#0f1c3f]">
                More from the blog
            </h2>

            <div class="mt-10 grid gap-8 sm:grid-cols-3">
                <Link
                    v-for="recent in recentBlogs"
                    :key="recent.id"
                    :href="showBlog({ slug: recent.slug })"
                    class="group block overflow-hidden rounded-lg border border-[#e7e1d4] bg-white"
                >
                    <div class="aspect-video overflow-hidden bg-[#eceae3]">
                        <LazyImage
                            v-if="recent.image_url"
                            :src="recent.image_url"
                            :alt="recent.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </div>
                    <div class="p-4">
                        <p
                            v-if="recent.category"
                            class="text-xs font-semibold tracking-[0.15em] text-[#b99a45] uppercase"
                        >
                            {{ recent.category.name }}
                        </p>
                        <h3
                            class="mt-1 font-serif text-base text-[#0f1c3f] group-hover:text-[#b99a45]"
                        >
                            {{ recent.title }}
                        </h3>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.blog-prose :deep(h2) {
    margin: 1.75rem 0 0.75rem;
    font-size: 1.5rem;
    color: #0f1c3f;
}

.blog-prose :deep(h3) {
    margin: 1.5rem 0 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f1c3f;
}

.blog-prose :deep(p) {
    margin-bottom: 1rem;
}

.blog-prose :deep(a) {
    color: #0f1c3f;
    text-decoration: underline;
    text-underline-offset: 3px;
}

.blog-prose :deep(ul) {
    margin: 0.5rem 0 1rem;
    list-style: disc;
    padding-inline-start: 1.5rem;
}

.blog-prose :deep(ol) {
    margin: 0.5rem 0 1rem;
    list-style: decimal;
    padding-inline-start: 1.5rem;
}
</style>
