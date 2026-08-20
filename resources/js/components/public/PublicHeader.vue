<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, ChevronDown, Menu, X } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { about, contact, home, privateLabel } from '@/routes';
import { request as requestCatalog } from '@/routes/catalog';
import { index as productsIndex } from '@/routes/products';

const navItems = [
    {
        title: 'Products',
        href: productsIndex(),
        matches: ['/products'],
        dropdown: true,
    },
    {
        title: 'Private Label',
        href: privateLabel(),
        matches: ['/private-label'],
        dropdown: false,
    },
    { title: 'About', href: about(), matches: ['/about'], dropdown: false },
    {
        title: 'Contact',
        href: contact(),
        matches: ['/contact'],
        dropdown: false,
    },
];

const page = usePage();

const logoUrl = computed(() => page.props.settings.logo_url);

const navProducts = computed(() => page.props.navProducts);

const slides = computed(() =>
    navProducts.value.filter((product) => product.image_url),
);

const slideIndex = ref(0);
const currentSlide = computed(() => slides.value[slideIndex.value] ?? null);

let slideTimer: ReturnType<typeof setInterval> | null = null;

function startSlideshow(): void {
    if (slides.value.length <= 1) {
        return;
    }

    slideTimer = setInterval(() => {
        slideIndex.value = (slideIndex.value + 1) % slides.value.length;
    }, 3500);
}

function stopSlideshow(): void {
    if (slideTimer) {
        clearInterval(slideTimer);
        slideTimer = null;
    }
}

onMounted(startSlideshow);
onBeforeUnmount(stopSlideshow);

const isProductsMenuOpen = ref(false);
let closeMenuTimer: ReturnType<typeof setTimeout> | null = null;

/**
 * Also bound to mousemove: mouseenter only fires when the cursor crosses into
 * the trigger, so a cursor already resting there when the header renders would
 * never open the menu.
 */
function openProductsMenu(): void {
    if (closeMenuTimer) {
        clearTimeout(closeMenuTimer);
        closeMenuTimer = null;
    }

    isProductsMenuOpen.value = true;
}

/**
 * Long enough to forgive a diagonal cursor path from the trigger to the panel.
 */
function scheduleCloseProductsMenu(): void {
    closeMenuTimer = setTimeout(() => {
        isProductsMenuOpen.value = false;
    }, 400);
}

const isMobileMenuOpen = ref(false);

const currentPath = computed(() => page.url.split('?')[0] ?? '/');

/**
 * The header persists across Inertia visits, so the drawer and the products
 * panel have to be closed manually once a navigation lands.
 */
watch(currentPath, () => {
    isMobileMenuOpen.value = false;

    if (closeMenuTimer) {
        clearTimeout(closeMenuTimer);
        closeMenuTimer = null;
    }

    isProductsMenuOpen.value = false;
});

function isActive(paths: string[]): boolean {
    return paths.some(
        (path) =>
            currentPath.value === path ||
            currentPath.value.startsWith(`${path}/`),
    );
}
</script>

<template>
    <header class="relative border-b border-[#e7e1d4] bg-white">
        <div
            class="relative mx-auto flex w-full flex-wrap items-center justify-between gap-y-2 px-2 py-3 md:px-6 lg:px-16 xl:px-20 2xl:px-24"
        >
            <Link :href="home()" class="flex items-center">
                <LazyImage
                    v-if="logoUrl"
                    loading="eager"
                    :src="logoUrl"
                    alt="Sevara"
                    class="h-9 max-w-40 object-contain"
                />
                <span
                    v-else
                    class="font-serif text-2xl font-semibold tracking-widest text-[#0f1c3f]"
                >
                    SEVARA
                </span>
            </Link>

            <nav
                class="order-last hidden w-full items-center justify-center gap-4 md:order-0 md:flex md:w-auto md:gap-6 lg:mr-auto lg:ml-24 lg:gap-10 xl:ml-32"
            >
                <div
                    v-for="item in navItems"
                    :key="item.title"
                    class="relative py-1.5"
                    @mouseenter="item.dropdown && openProductsMenu()"
                    @mousemove="item.dropdown && openProductsMenu()"
                    @mouseleave="item.dropdown && scheduleCloseProductsMenu()"
                    @focusin="item.dropdown && openProductsMenu()"
                    @focusout="item.dropdown && scheduleCloseProductsMenu()"
                >
                    <Link
                        :href="item.href"
                        class="relative flex items-center gap-1 py-1 text-xs font-semibold tracking-[0.15em] uppercase transition-colors"
                        :class="
                            isActive(item.matches)
                                ? 'text-[#0f1c3f]'
                                : 'text-[#3a3a35] hover:text-primeGold/70'
                        "
                    >
                        {{ item.title }}
                        <ChevronDown
                            v-if="item.dropdown"
                            class="size-3 transition-transform duration-300"
                            :class="isProductsMenuOpen && 'rotate-180'"
                        />
                        <span
                            v-if="isActive(item.matches)"
                            class="absolute right-0 -bottom-0.5 left-0 h-px bg-[#d4af37]"
                        />
                    </Link>

                    <Transition
                        v-if="item.dropdown"
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="-translate-y-2 scale-[0.98] opacity-0"
                        enter-to-class="translate-y-0 scale-100 opacity-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="translate-y-0 scale-100 opacity-100"
                        leave-to-class="-translate-y-2 scale-[0.98] opacity-0"
                    >
                        <div
                            v-if="isProductsMenuOpen"
                            class="absolute top-full left-0 z-50 w-104 origin-top pt-3 lg:w-152 xl:w-[calc(100vw-36rem)] 2xl:w-[calc(100vw-40rem)]"
                            @mouseenter="openProductsMenu"
                            @mouseleave="scheduleCloseProductsMenu"
                        >
                            <div
                                class="overflow-hidden rounded-2xl border border-[#d4af37]/30 bg-[#fdfbf6] shadow-[0_24px_70px_-28px_rgba(15,28,63,0.45),0_8px_24px_-16px_rgba(15,28,63,0.3)]"
                            >
                                <div
                                    class="lg:grid lg:min-h-96 lg:grid-cols-[minmax(18rem,0.82fr)_minmax(0,1.18fr)]"
                                >
                                    <Link
                                        v-if="currentSlide"
                                        :href="currentSlide.href"
                                        class="group/feature relative block h-52 overflow-hidden bg-[#0f1c3f] lg:h-auto"
                                    >
                                        <LazyImage
                                            v-for="(slide, index) in slides"
                                            :key="slide.href"
                                            :src="slide.image_url ?? ''"
                                            :alt="slide.title"
                                            class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out"
                                            :class="
                                                index === slideIndex
                                                    ? 'opacity-100'
                                                    : 'opacity-0'
                                            "
                                        />
                                        <div
                                            class="absolute inset-0 bg-linear-to-t from-[#0f1c3f] via-[#0f1c3f]/35 to-transparent"
                                        />
                                        <div
                                            class="absolute inset-x-0 bottom-0 p-6 lg:p-7"
                                        >
                                            <p
                                                class="text-[10px] font-semibold tracking-[0.3em] text-[#e9d9ae] uppercase"
                                            >
                                                Featured collection
                                            </p>
                                            <p
                                                class="mt-2 font-serif text-2xl leading-tight text-white"
                                            >
                                                {{ currentSlide.title }}
                                            </p>
                                            <span
                                                class="mt-5 inline-flex items-center gap-2 text-[10px] font-semibold tracking-[0.2em] text-[#d4af37] uppercase"
                                            >
                                                Explore collection
                                                <ArrowRight
                                                    class="size-3.5 transition-transform duration-300 group-hover/feature:translate-x-1"
                                                />
                                            </span>
                                        </div>
                                    </Link>

                                    <div
                                        class="min-w-0 px-5 py-6 sm:px-6 lg:px-8 lg:py-7"
                                    >
                                        <p
                                            class="text-[10px] font-semibold tracking-[0.3em] text-[#b99a45] uppercase"
                                        >
                                            Our Collections
                                        </p>
                                        <p
                                            class="mt-2 font-serif text-2xl text-primeBlue"
                                        >
                                            Discover Sevara dates
                                        </p>
                                        <p
                                            class="mt-2 text-sm leading-6 text-[#6b6558]"
                                        >
                                            Crafted for everyday ritual and
                                            exceptional gifting.
                                        </p>

                                        <p
                                            v-if="navProducts.length === 0"
                                            class="py-8 text-sm text-[#8a8a7f]"
                                        >
                                            Coming soon.
                                        </p>

                                        <div
                                            class="mt-6 grid gap-2 sm:grid-cols-2"
                                        >
                                            <Link
                                                v-for="product in navProducts"
                                                :key="product.href"
                                                :href="product.href"
                                                class="group/item flex items-center gap-3 rounded-xl border border-[#e7e1d4] bg-white/60 p-3 text-[#3a3a35] transition-all duration-300 hover:-translate-y-0.5 hover:border-[#d4af37]/60 hover:bg-[#f7f3e8] hover:shadow-[0_14px_24px_-20px_rgba(15,28,63,0.5)]"
                                            >
                                                <span
                                                    class="size-13 shrink-0 overflow-hidden rounded-lg border border-[#d4af37]/20 bg-[#f7f3e8]/60"
                                                >
                                                    <LazyImage
                                                        v-if="product.image_url"
                                                        :src="product.image_url"
                                                        :alt="product.title"
                                                        class="h-full w-full object-cover"
                                                    />
                                                </span>
                                                <span
                                                    class="min-w-0 flex-1 font-serif text-base leading-snug tracking-wide"
                                                >
                                                    {{ product.title }}
                                                </span>
                                                <ArrowRight
                                                    class="size-4 shrink-0 -translate-x-1 text-[#d4af37] opacity-0 transition-all duration-300 group-hover/item:translate-x-0 group-hover/item:opacity-100"
                                                />
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <Link
                                    :href="productsIndex()"
                                    class="flex items-center justify-between gap-2 border-t border-[#d4af37]/30 bg-[#0f1c3f] px-6 py-4 text-[11px] font-semibold tracking-[0.2em] text-[#e9d9ae] uppercase transition-colors hover:bg-[#162851] lg:px-8"
                                >
                                    Explore the full collection
                                    <ArrowRight class="size-4 text-[#d4af37]" />
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </nav>

            <div class="hidden md:block">
                <Button
                    as-child
                    class="h-9 rounded-xs bg-[#0f1c3f] text-xs font-semibold tracking-[0.15em] text-lightGold uppercase hover:bg-[#0f1c3f]/90"
                >
                    <Link :href="requestCatalog()">
                        <BookOpen />
                        Request Catalog
                    </Link>
                </Button>
            </div>

            <Sheet v-model:open="isMobileMenuOpen">
                <SheetTrigger as-child>
                    <button
                        type="button"
                        aria-label="Open menu"
                        class="flex size-10 items-center justify-center rounded-xs text-primeBlue transition hover:bg-[#f7f3e8] md:hidden"
                    >
                        <Menu class="size-6" />
                    </button>
                </SheetTrigger>

                <SheetContent
                    side="right"
                    class="w-72 border-l-[#e7e1d4] bg-[#fdfbf6] p-0 sm:max-w-xs [&>button:last-child]:hidden"
                >
                    <SheetTitle class="sr-only">Navigation menu</SheetTitle>

                    <div
                        class="flex items-center justify-between border-b border-[#e7e1d4] px-6 py-4"
                    >
                        <LazyImage
                            v-if="logoUrl"
                            loading="eager"
                            :src="logoUrl"
                            alt="Sevara"
                            class="h-8 max-w-32 object-contain"
                        />
                        <span
                            v-else
                            class="font-serif text-xl font-semibold tracking-widest text-primeBlue"
                        >
                            SEVARA
                        </span>

                        <button
                            type="button"
                            aria-label="Close menu"
                            class="flex size-9 items-center justify-center rounded-xs text-primeBlue transition hover:bg-[#f7f3e8]"
                            @click="isMobileMenuOpen = false"
                        >
                            <X class="size-5" />
                        </button>
                    </div>

                    <nav class="flex flex-col px-2 py-4">
                        <template v-for="item in navItems" :key="item.title">
                            <Link
                                :href="item.href"
                                class="border-l-2 px-4 py-3 text-xs font-semibold tracking-[0.15em] uppercase transition-colors"
                                :class="
                                    isActive(item.matches)
                                        ? 'border-[#d4af37] bg-[#f7f3e8] text-primeBlue'
                                        : 'border-transparent text-[#3a3a35] hover:border-primeGold/40 hover:text-primeGold/70'
                                "
                            >
                                {{ item.title }}
                            </Link>

                            <Link
                                v-for="product in item.dropdown
                                    ? navProducts
                                    : []"
                                :key="product.href"
                                :href="product.href"
                                class="flex items-center justify-between gap-3 py-2.5 pr-4 pl-9 text-sm text-[#6b6558] transition-colors hover:text-primeBlue"
                            >
                                <span class="font-serif">
                                    {{ product.title }}
                                </span>
                                <ArrowRight class="size-3.5 text-[#d4af37]" />
                            </Link>
                        </template>
                    </nav>

                    <div class="mt-auto border-t border-[#e7e1d4] p-6">
                        <Link
                            :href="requestCatalog()"
                            class="flex items-center justify-center gap-2 bg-primeBlue px-6 py-3 text-xs font-semibold tracking-[0.15em] text-lightGold uppercase transition hover:bg-primeBlue/90"
                        >
                            <BookOpen class="size-4" />
                            Request Catalog
                        </Link>
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </header>
</template>
