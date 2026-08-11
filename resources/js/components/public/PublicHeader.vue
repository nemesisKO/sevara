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
            class="relative mx-auto flex w-full flex-wrap items-center justify-between gap-y-3 px-2 py-4 md:px-6 lg:px-24"
        >
            <Link :href="home()" class="flex items-center">
                <LazyImage
                    v-if="logoUrl"
                    loading="eager"
                    :src="logoUrl"
                    alt="Sevara"
                    class="h-10 max-w-40 object-contain"
                />
                <span
                    v-else
                    class="font-serif text-2xl font-semibold tracking-widest text-[#0f1c3f]"
                >
                    SEVARA
                </span>
            </Link>

            <nav
                class="order-last hidden w-full items-center justify-center gap-4 md:order-0 md:flex md:w-auto md:gap-6 lg:gap-10"
            >
                <div
                    v-for="item in navItems"
                    :key="item.title"
                    class="relative py-2"
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
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="-translate-y-1 scale-98 opacity-0"
                        enter-to-class="translate-y-0 scale-100 opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="translate-y-0 scale-100 opacity-100"
                        leave-to-class="-translate-y-1 scale-98 opacity-0"
                    >
                        <div
                            v-if="isProductsMenuOpen"
                            class="absolute top-full left-0 z-50 origin-top pt-1.5"
                            @mouseenter="openProductsMenu"
                            @mouseleave="scheduleCloseProductsMenu"
                        >
                            <div
                                class="w-88 overflow-hidden rounded-xl border border-[#e7e1d4] bg-white shadow-[0_28px_60px_-24px_rgba(15,28,63,0.45)] lg:w-152 xl:w-168"
                            >
                                <div class="lg:flex lg:items-stretch">
                                    <Link
                                        v-if="currentSlide"
                                        :href="currentSlide.href"
                                        class="relative block h-36 shrink-0 overflow-hidden bg-[#0f1c3f] lg:h-auto lg:w-60"
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
                                            class="absolute inset-0 bg-linear-to-t from-[#0f1c3f] via-[#0f1c3f]/25 to-transparent"
                                        />
                                        <div
                                            class="absolute inset-x-0 bottom-0 p-4"
                                        >
                                            <p
                                                class="text-[9px] font-semibold tracking-[0.3em] text-[#d4af37] uppercase"
                                            >
                                                Featured
                                            </p>
                                            <p
                                                class="mt-1 font-serif text-base leading-snug text-white"
                                            >
                                                {{ currentSlide.title }}
                                            </p>
                                        </div>
                                    </Link>

                                    <div class="min-w-0 flex-1 p-3 lg:p-4">
                                        <p
                                            class="px-2 text-[10px] font-semibold tracking-[0.3em] text-[#b99a45] uppercase"
                                        >
                                            Our Collections
                                        </p>

                                        <p
                                            v-if="navProducts.length === 0"
                                            class="px-2 py-6 text-sm text-[#8a8a7f]"
                                        >
                                            Coming soon.
                                        </p>

                                        <div class="mt-2 flex flex-col">
                                            <Link
                                                v-for="product in navProducts"
                                                :key="product.href"
                                                :href="product.href"
                                                class="group/item flex items-center gap-3 rounded-lg p-2 text-[#3a3a35] transition-colors duration-200 hover:bg-[#f7f3e8] hover:text-[#0f1c3f]"
                                            >
                                                <span
                                                    class="size-11 shrink-0 overflow-hidden rounded-md border border-[#efe9db] bg-[#f7f3e8]/60"
                                                >
                                                    <LazyImage
                                                        v-if="product.image_url"
                                                        :src="product.image_url"
                                                        :alt="product.title"
                                                        class="h-full w-full object-cover"
                                                    />
                                                </span>
                                                <span
                                                    class="min-w-0 flex-1 truncate font-serif text-[15px] tracking-wide"
                                                >
                                                    {{ product.title }}
                                                </span>
                                                <ArrowRight
                                                    class="size-4 shrink-0 -translate-x-1 text-[#d4af37] opacity-0 transition-all duration-200 group-hover/item:translate-x-0 group-hover/item:opacity-100"
                                                />
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <Link
                                    :href="productsIndex()"
                                    class="flex items-center justify-between gap-2 border-t border-[#efe9db] bg-[#fdfbf6] px-5 py-3.5 text-[11px] font-semibold tracking-[0.15em] text-[#0f1c3f] uppercase transition-colors hover:bg-[#f7f3e8]"
                                >
                                    Shop all products
                                    <ArrowRight
                                        class="size-3.5 text-[#d4af37]"
                                    />
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </nav>

            <div class="hidden md:block">
                <Button
                    as-child
                    class="rounded-xs bg-[#0f1c3f] text-xs font-semibold tracking-[0.15em] text-lightGold uppercase hover:bg-[#0f1c3f]/90"
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
