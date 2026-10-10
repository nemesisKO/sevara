<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import {
    ArrowDown,
    BookOpen,
    Check,
    ChevronDown,
    Coffee,
    ConciergeBell,
    Diamond,
    Download,
    Gift,
    Globe,
    Plus,
    Package,
    Ship,
    ShoppingBag,
    Tag,
    Users,
} from '@lucide/vue';
import { computed, ref, useTemplateRef } from 'vue';
import JsonLd from '@/components/JsonLd.vue';
import LazyImage from '@/components/LazyImage.vue';
import PrivateLabelBanner from '@/components/public/PrivateLabelBanner.vue';
import ProductCollection from '@/components/public/ProductCollection.vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';
import SampleRequestModal from '@/components/public/SampleRequestModal.vue';
import type { Product } from '@/types';

const props = defineProps<{
    product: Product;
    otherProducts: Product[];
    sampleVolumes: string[];
    shippingTerms: string[];
}>();

const page = usePage();

const productName = computed(() =>
    `${props.product.title_part1} ${props.product.title_part2}`.trim(),
);

const metaDescription = computed(
    () =>
        props.product.description ??
        props.product.subtitle ??
        page.props.seo.description,
);

const structuredData = computed(() => {
    const seo = page.props.seo;
    const origin = seo.url;

    return {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: productName.value,
        description: metaDescription.value,
        url: `${origin}/products/${props.product.slug}`,
        image: props.product.images.map(
            (image) => `${origin}${image.image_url}`,
        ),
        brand: {
            '@type': 'Brand',
            name: seo.name,
        },
        ...(props.product.specifications.length > 0
            ? {
                  additionalProperty: props.product.specifications.map(
                      (specification) => ({
                          '@type': 'PropertyValue',
                          name: specification.question,
                          value: specification.answer,
                      }),
                  ),
              }
            : {}),
    };
});

const activeIndex = ref(0);
const isSampleRequestOpen = ref(false);

const activeImage = computed(
    () => props.product.images[activeIndex.value] ?? props.product.images[0],
);

const technicalDetails = computed(() => [
    { title: 'Net piece weight', value: props.product.net_piece_weight },
    { title: 'Pieces / pack', value: props.product.pieces_per_pack },
    { title: 'Shelf life', value: props.product.shelf_life },
    { title: 'Storage', value: props.product.storage },
    { title: 'Allergens', value: props.product.allergens },
    { title: 'MOQ', value: props.product.moq },
]);

const hasTechnicalContent = computed(() =>
    Boolean(
        technicalDetails.value.some((detail) => detail.value) ||
        props.product.technical_data_sheet_url ||
        props.product.pieces_per_pack_url,
    ),
);

const exploreSection = useTemplateRef('exploreSection');

function scrollToExplore(): void {
    exploreSection.value?.scrollIntoView({ behavior: 'smooth' });
}

function openCatalogueRequestModal(): void {
    window.dispatchEvent(new Event('open-catalogue-request-modal'));
}

const openSpec = ref<number | null>(null);

function toggleSpec(id: number): void {
    openSpec.value = openSpec.value === id ? null : id;
}

const features = [
    { icon: ShoppingBag, title: 'Retail Ready' },
    { icon: Tag, title: 'Private Label Available' },
    { icon: Globe, title: 'Export Focused' },
];

const markets = [
    { icon: Ship, title: 'Importers' },
    { icon: Users, title: 'Distributors' },
    { icon: Gift, title: 'Retail & Gift' },
    { icon: Coffee, title: 'Coffee Shops' },
    { icon: ConciergeBell, title: 'Hotels' },
];

const whyChooseFeatures = [
    'Premium Packaging',
    'Whole Almond',
    'Private Label',
    'Export Focus',
];

const faqs = [
    {
        id: 1,
        question: 'Do you provide product samples?',
        answer: 'Yes, we’re happy to arrange product samples so you can experience the quality of Sevara firsthand before placing your order.',
    },
    {
        id: 2,
        question: 'What packaging formats are available?',
        answer: 'We offer a range of packaging formats and can tailor sizes, materials, and presentation to fit your market and retail needs.',
    },
    {
        id: 3,
        question: 'Can we create our own private label?',
        answer: 'Absolutely. Private labeling is one of our core strengths, and our team works closely with partners to bring their own brand to life.',
    },
    {
        id: 4,
        question: 'Which countries do you export to?',
        answer: 'We proudly export to a growing number of countries around the world and are always open to exploring new markets and partnerships.',
    },
];

const openFaq = ref<number | null>(null);

function toggleFaq(id: number): void {
    openFaq.value = openFaq.value === id ? null : id;
}
</script>

<template>
    <Head :title="product.seo_title">
        <meta
            head-key="description"
            name="description"
            :content="metaDescription"
        />
        <meta head-key="og:type" property="og:type" content="product" />
        <meta
            head-key="og:title"
            property="og:title"
            :content="product.seo_title"
        />
        <meta
            head-key="og:description"
            property="og:description"
            :content="metaDescription"
        />
        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="product.seo_title"
        />
        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="metaDescription"
        />
    </Head>
    <JsonLd :data="structuredData" head-key="product" />

    <section class="flex min-h-[78svh] items-center overflow-hidden">
        <div
            class="grid w-full items-center gap-8 px-6 py-10 lg:grid-cols-[6rem_minmax(0,1.1fr)_minmax(0,1fr)] lg:gap-4 lg:px-12 lg:py-8 xl:px-20"
        >
            <div
                class="order-2 flex items-center justify-center lg:order-1 lg:flex-col"
            >
                <div
                    v-if="product.images.length > 1"
                    class="flex flex-row items-center gap-3 lg:flex-col"
                >
                    <template
                        v-for="(image, index) in product.images"
                        :key="image.id"
                    >
                        <button
                            type="button"
                            class="group flex cursor-pointer flex-col items-center gap-2.5 rounded-lg p-2 transition-transform duration-300 hover:scale-110 focus-visible:ring-2 focus-visible:ring-[#b99a45]/50 focus-visible:outline-none"
                            :aria-label="`Show image ${index + 1}`"
                            @click="activeIndex = index"
                        >
                            <span
                                class="font-serif text-lg tracking-wide transition-colors duration-300"
                                :class="
                                    index === activeIndex
                                        ? 'text-[#0f1c3f]'
                                        : 'text-[#a89f8d] group-hover:text-[#0f1c3f]'
                                "
                            >
                                {{ String(index + 1).padStart(2, '0') }}
                            </span>
                            <span
                                class="size-4 rounded-full transition-all duration-300"
                                :class="
                                    index === activeIndex
                                        ? 'bg-[#b99a45] ring-4 ring-[#b99a45]/25'
                                        : 'border-2 border-[#b99a45]/60 group-hover:border-[#b99a45] group-hover:bg-[#b99a45]/20'
                                "
                            />
                        </button>
                        <span
                            v-if="index < product.images.length - 1"
                            class="h-px w-8 bg-[#d4af37]/40 lg:h-10 lg:w-px"
                        />
                    </template>
                </div>

                <button
                    type="button"
                    class="mt-14 hidden flex-col items-center gap-4 lg:flex"
                    @click="scrollToExplore"
                >
                    <span
                        class="rotate-180 text-[10px] font-semibold tracking-[0.3em] text-primeGold uppercase [writing-mode:vertical-rl]"
                    >
                        Scroll to explore
                    </span>
                    <ArrowDown class="size-4 animate-bounce text-primeGold" />
                </button>
            </div>

            <div class="order-1 lg:order-2">
                <div
                    class="relative flex h-[42svh] items-center justify-center lg:h-[62svh]"
                >
                    <Transition name="gallery" mode="out-in">
                        <LazyImage
                            v-if="activeImage"
                            :key="activeImage.id"
                            :src="activeImage.image_url"
                            :alt="product.seo_title"
                            class="h-full w-auto max-w-full object-contain mix-blend-multiply"
                        />
                        <span v-else class="text-sm text-[#9a9488]">
                            No image available
                        </span>
                    </Transition>
                </div>
            </div>

            <div class="order-3 lg:pl-6 xl:pl-14">
                <h1
                    class="font-serif text-3xl leading-tight lg:text-4xl 2xl:text-5xl"
                >
                    <span
                        class="block whitespace-nowrap"
                        :style="{ color: product.title_part1_color }"
                    >
                        {{ product.title_part1 }}
                    </span>
                    <span
                        class="block whitespace-nowrap"
                        :style="{ color: product.title_part2_color }"
                    >
                        {{ product.title_part2 }}
                    </span>
                </h1>

                <div class="mt-6 flex items-center gap-4">
                    <span class="h-px w-16 bg-[#d4af37]/40" />
                    <Diamond class="size-3 text-[#d4af37]" />
                    <span class="h-px w-16 bg-[#d4af37]/40" />
                </div>

                <p
                    v-if="product.subtitle"
                    class="mt-6 text-lg"
                    :style="{ color: product.subtitle_color }"
                >
                    {{ product.subtitle }}
                </p>

                <p
                    v-if="product.description"
                    class="mt-4 max-w-md text-[#6b6558]"
                >
                    {{ product.description }}
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-none bg-[#0f1c3f] px-6 py-3 text-sm font-medium tracking-wide text-white transition hover:bg-[#0f1c3f]/90"
                        @click="isSampleRequestOpen = true"
                    >
                        <Package class="size-4" />
                        Request Sample
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-none border border-[#0f1c3f] px-6 py-3 text-sm font-medium tracking-wide text-[#0f1c3f] transition hover:bg-[#0f1c3f]/5"
                        @click="openCatalogueRequestModal"
                    >
                        <BookOpen class="size-4" />
                        Request Catalog
                    </button>
                </div>

                <SampleRequestModal
                    v-model:open="isSampleRequestOpen"
                    :product="product"
                    :sample-volumes="sampleVolumes"
                    :shipping-terms="shippingTerms"
                />

                <div
                    v-if="product.experience_items.length > 0"
                    class="mt-8 grid max-w-sm grid-cols-2 gap-x-6 gap-y-5"
                >
                    <RevealOnScroll
                        v-for="(item, index) in product.experience_items"
                        :key="item.id"
                        :delay="index * 100"
                    >
                        <div
                            class="flex h-full flex-col items-center gap-3 border-t border-[#d4af37]/35 pt-4 text-center"
                        >
                            <LazyImage
                                :src="item.icon_url"
                                alt=""
                                class="size-9 object-contain"
                            />
                            <p
                                class="text-xs font-semibold tracking-[0.12em] text-[#0f1c3f] uppercase"
                            >
                                {{ item.title }}
                            </p>
                        </div>
                    </RevealOnScroll>
                </div>
            </div>
        </div>
    </section>

    <section
        v-if="hasTechnicalContent"
        class="mx-auto max-w-6xl px-6 pb-16 lg:px-10"
    >
        <div class="grid gap-6 border-y border-[#e7e1d4] py-7">
            <div class="grid grid-cols-2 gap-y-6 sm:grid-cols-3 lg:grid-cols-6">
                <div
                    v-for="detail in technicalDetails"
                    :key="detail.title"
                    class="border-[#e7e1d4] px-4 text-center lg:border-r lg:last:border-r-0"
                >
                    <p
                        class="text-[10px] font-semibold tracking-[0.16em] text-[#9a9386] uppercase"
                    >
                        {{ detail.title }}
                    </p>
                    <p class="mt-2 text-sm font-medium text-[#0f1c3f]">
                        {{ detail.value || '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a
                    v-if="product.technical_data_sheet_url"
                    :href="product.technical_data_sheet_url"
                    download
                    class="inline-flex items-center gap-2 rounded-none border border-[#0f1c3f] px-5 py-2.5 text-xs font-semibold tracking-wide text-[#0f1c3f] uppercase transition hover:bg-[#0f1c3f]/5"
                >
                    <Download class="size-4" />
                    View technical data sheet
                </a>
                <a
                    v-if="product.pieces_per_pack_url"
                    :href="product.pieces_per_pack_url"
                    download
                    class="inline-flex items-center gap-2 rounded-none bg-[#0f1c3f] px-5 py-2.5 text-xs font-semibold tracking-wide text-lightGold uppercase transition hover:bg-[#0f1c3f]/90"
                >
                    <Download class="size-4" />
                    View full pieces/pack
                </a>
            </div>
        </div>
    </section>

    <section
        ref="exploreSection"
        class="mx-auto max-w-6xl scroll-mt-10 px-6 pb-24 lg:px-10"
    >
        <div class="grid gap-6 sm:grid-cols-3">
            <RevealOnScroll
                v-for="(feature, index) in features"
                :key="feature.title"
                :delay="index * 130"
            >
                <div
                    class="flex h-full flex-col items-center gap-5 rounded-lg bg-[#f7f3e8]/70 px-6 py-12 text-center transition-transform duration-300 hover:scale-[1.03]"
                >
                    <component
                        :is="feature.icon"
                        class="size-9 text-primeGold"
                        :stroke-width="1.25"
                    />
                    <p
                        class="text-sm font-semibold tracking-[0.2em] text-[#0f1c3f] uppercase"
                    >
                        {{ feature.title }}
                    </p>
                    <span class="h-px w-8 bg-[#d4af37]/50" />
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <PrivateLabelBanner class="pb-24" />

    <section class="mx-auto max-w-6xl px-6 pb-28 lg:px-4">
        <RevealOnScroll>
            <div class="text-center">
                <h2 class="font-serif text-3xl text-[#0f1c3f] sm:text-4xl">
                    Made for Modern Markets
                </h2>
                <div class="mt-5 flex items-center justify-center gap-4">
                    <span class="h-px w-12 bg-[#d4af37]/40" />
                    <Diamond class="size-2.5 text-[#d4af37]" />
                    <span class="h-px w-12 bg-[#d4af37]/40" />
                </div>
            </div>
        </RevealOnScroll>

        <div
            class="mt-14 grid grid-cols-2 gap-y-12 sm:grid-cols-3 lg:grid-cols-5 lg:divide-x lg:divide-[#e7e1d4]"
        >
            <RevealOnScroll
                v-for="(market, index) in markets"
                :key="market.title"
                :delay="index * 110"
            >
                <div class="flex flex-col items-center gap-4 px-4 text-center">
                    <component
                        :is="market.icon"
                        class="size-9 text-primeGold"
                        :stroke-width="1.25"
                    />
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-[#0f1c3f] uppercase"
                    >
                        {{ market.title }}
                    </p>
                </div>
            </RevealOnScroll>
        </div>
    </section>

    <ProductCollection :products="otherProducts" />

    <section
        v-if="product.specifications.length > 0"
        class="mx-auto max-w-6xl px-6 pb-28 lg:px-4"
    >
        <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-14">
            <RevealOnScroll class="lg:col-span-5">
                <div
                    class="divide-y divide-[#e7e1d4] border-y border-[#e7e1d4]"
                >
                    <div v-for="spec in product.specifications" :key="spec.id">
                        <button
                            type="button"
                            class="group flex w-full cursor-pointer items-center gap-5 py-6 text-left"
                            :aria-expanded="openSpec === spec.id"
                            @click="toggleSpec(spec.id)"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center"
                            >
                                <LazyImage
                                    :src="spec.icon_url"
                                    alt=""
                                    class="max-h-full max-w-full object-contain"
                                />
                            </span>
                            <span
                                class="flex-1 text-sm font-semibold tracking-[0.15em] text-[#0f1c3f] uppercase transition-colors group-hover:text-primeGold"
                            >
                                {{ spec.question }}
                            </span>
                            <Plus
                                class="size-5 shrink-0 text-primeGold transition-transform duration-300"
                                :class="openSpec === spec.id && 'rotate-45'"
                                :stroke-width="2"
                            />
                        </button>
                        <div
                            class="grid transition-[grid-template-rows] duration-300 ease-out"
                            :class="
                                openSpec === spec.id
                                    ? 'grid-rows-[1fr]'
                                    : 'grid-rows-[0fr]'
                            "
                        >
                            <div class="overflow-hidden">
                                <p class="pb-6 pl-16 text-[#6b6558]">
                                    {{ spec.answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </RevealOnScroll>

            <RevealOnScroll :delay="150" class="lg:col-span-7">
                <LazyImage
                    v-if="product.spec_image_url"
                    :src="product.spec_image_url"
                    alt=""
                    class="aspect-video w-full rounded-xl object-cover"
                />
                <div
                    v-else
                    class="aspect-video w-full rounded-xl bg-[#eceae3]"
                />
            </RevealOnScroll>
        </div>
    </section>

    <section class="container mx-auto px-6 pb-28 font-serif lg:px-10">
        <div class="grid gap-16 lg:grid-cols-2 lg:gap-12">
            <div>
                <RevealOnScroll>
                    <div class="text-center">
                        <h2
                            class="font-serif text-3xl text-[#0f1c3f] sm:text-4xl"
                        >
                            <span class="text-[#d4af37]">—</span>
                            Why Choose Sevara
                            <span class="text-[#d4af37]">—</span>
                        </h2>
                    </div>
                </RevealOnScroll>

                <RevealOnScroll :delay="100">
                    <div
                        class="mt-10 overflow-x-auto rounded-lg border border-[#e7e1d4]"
                    >
                        <table
                            class="w-full min-w-120 border-collapse text-left"
                        >
                            <thead>
                                <tr class="bg-[#0f1c3f]">
                                    <th
                                        class="px-6 py-4 text-xs font-semibold tracking-[0.2em] text-white uppercase"
                                    >
                                        Features
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold tracking-[0.2em] text-[#d4af37] uppercase"
                                    >
                                        Sevara
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold tracking-[0.2em] text-white uppercase"
                                    >
                                        Typical Products
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e7e1d4]">
                                <tr
                                    v-for="feature in whyChooseFeatures"
                                    :key="feature"
                                    class="odd:bg-[#f7f3e8]/40"
                                >
                                    <td
                                        class="px-6 py-4 text-sm text-[#0f1c3f]"
                                    >
                                        {{ feature }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Check
                                            class="mx-auto size-4 text-primeGold"
                                        />
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center text-[#a89f8d]"
                                    >
                                        —
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </RevealOnScroll>
            </div>

            <div>
                <RevealOnScroll :delay="150">
                    <div class="text-center">
                        <h2
                            class="font-serif text-3xl text-[#0f1c3f] sm:text-4xl"
                        >
                            <span class="text-[#d4af37]">—</span>
                            Frequently Asked Questions
                            <span class="text-[#d4af37]">—</span>
                        </h2>
                    </div>
                </RevealOnScroll>

                <RevealOnScroll :delay="200">
                    <div
                        class="mt-10 divide-y divide-[#e7e1d4] border-y border-[#e7e1d4]"
                    >
                        <div v-for="faq in faqs" :key="faq.id">
                            <button
                                type="button"
                                class="group flex w-full cursor-pointer items-center justify-between gap-5 py-5 text-left"
                                :aria-expanded="openFaq === faq.id"
                                @click="toggleFaq(faq.id)"
                            >
                                <span
                                    class="text-sm font-medium text-[#0f1c3f] transition-colors group-hover:text-primeGold"
                                >
                                    {{ faq.question }}
                                </span>
                                <ChevronDown
                                    class="size-4 shrink-0 text-primeGold transition-transform duration-300"
                                    :class="openFaq === faq.id && 'rotate-180'"
                                />
                            </button>
                            <div
                                class="grid transition-[grid-template-rows] duration-300 ease-out"
                                :class="
                                    openFaq === faq.id
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
                </RevealOnScroll>
            </div>
        </div>
    </section>
</template>

<style scoped>
.gallery-enter-active,
.gallery-leave-active {
    transition:
        opacity 0.4s ease,
        transform 0.4s ease;
}

.gallery-enter-from {
    opacity: 0;
    transform: translateY(24px) scale(0.97);
}

.gallery-leave-to {
    opacity: 0;
    transform: translateY(-24px) scale(0.97);
}
</style>
