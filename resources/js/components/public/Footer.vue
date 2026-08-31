<script setup lang="ts">
import { Form, Link, usePage } from '@inertiajs/vue3';
import { Mail, MapPin, Phone, Send } from '@lucide/vue';
import { computed } from 'vue';
import NewsletterSubscriptionController from '@/actions/App/Http/Controllers/NewsletterSubscriptionController';
import LazyImage from '@/components/LazyImage.vue';
import { about, contact, home, privateLabel } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
import { index as productsIndex } from '@/routes/products';

const page = usePage();

const logoUrl = computed(() => page.props.settings.logo_url);

const navItems = [
    { title: 'Products', href: productsIndex() },
    { title: 'Private Label', href: privateLabel() },
    { title: 'About', href: about() },
    { title: 'Blog', href: blogIndex() },
    { title: 'Contact', href: contact() },
];

const productItems = [
    'Strawberry Chocolate Date',
    'Dark Chocolate Date',
    'Milk Chocolate Date',
    'Coffee Chocolate Date',
    'White Chocolate Date',
];

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="bg-[#0f1c3f] text-[#aab0c2]">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-10">
            <div
                class="grid gap-12 lg:grid-cols-[1.3fr_0.9fr_1fr_1.1fr] lg:gap-8"
            >
                <div>
                    <Link :href="home()" class="inline-flex items-center">
                        <LazyImage
                            v-if="logoUrl"
                            :src="logoUrl"
                            alt="Sevara"
                            class="h-9 max-w-40 object-contain"
                        />
                        <span
                            v-else
                            class="font-serif text-2xl tracking-widest text-white"
                        >
                            SEVARA
                        </span>
                    </Link>
                    <p
                        class="mt-1 text-[10px] tracking-[0.3em] text-[#b99a45] uppercase"
                    >
                        Premium Dates
                    </p>

                    <h3
                        class="mt-8 text-xs font-semibold tracking-[0.25em] text-white uppercase"
                    >
                        Stay Updated
                    </h3>
                    <p class="mt-3 text-sm text-[#aab0c2]">
                        Exclusive updates and product insights.
                    </p>

                    <Form
                        v-bind="NewsletterSubscriptionController.store.form()"
                        reset-on-success
                        class="mt-4 max-w-xs"
                        v-slot="{ errors, processing, wasSuccessful }"
                    >
                        <div class="flex">
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="Enter your email"
                                class="w-full border border-white/15 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-[#aab0c2] focus:border-[#d4af37] focus:outline-none"
                                :disabled="processing || wasSuccessful"
                            />
                            <button
                                type="submit"
                                aria-label="Subscribe"
                                :disabled="processing || wasSuccessful"
                                class="flex shrink-0 items-center justify-center bg-primeGold px-4 text-[#0f1c3f] transition hover:bg-[#c9a233]"
                            >
                                <Send class="size-4" />
                            </button>
                        </div>
                        <p
                            v-if="errors.email"
                            class="mt-2 text-xs text-red-200"
                        >
                            {{ errors.email }}
                        </p>
                        <p
                            v-else-if="wasSuccessful"
                            class="mt-2 text-xs text-emerald-200"
                        >
                            You’re on the list. Thank you.
                        </p>
                    </Form>

                    <div class="mt-6 flex items-center gap-3">
                        <a
                            href="#"
                            aria-label="Instagram"
                            class="flex size-9 items-center justify-center rounded-full border border-white/20 text-[#aab0c2] transition hover:border-[#d4af37] hover:text-[#d4af37]"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="size-4"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="5"
                                />
                                <circle cx="12" cy="12" r="4" />
                                <circle
                                    cx="17.2"
                                    cy="6.8"
                                    r="0.6"
                                    fill="currentColor"
                                    stroke="none"
                                />
                            </svg>
                        </a>
                        <a
                            href="#"
                            aria-label="LinkedIn"
                            class="flex size-9 items-center justify-center rounded-full border border-white/20 text-[#aab0c2] transition hover:border-[#d4af37] hover:text-[#d4af37]"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="size-4"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="2"
                                />
                                <line x1="7" y1="10.5" x2="7" y2="17" />
                                <circle
                                    cx="7"
                                    cy="6.8"
                                    r="0.6"
                                    fill="currentColor"
                                    stroke="none"
                                />
                                <path d="M11 17v-4.2a2.3 2.3 0 0 1 4.6 0V17" />
                                <line x1="11" y1="10.5" x2="11" y2="17" />
                            </svg>
                        </a>
                        <a
                            href="#"
                            aria-label="Facebook"
                            class="flex size-9 items-center justify-center rounded-full border border-white/20 text-[#aab0c2] transition hover:border-[#d4af37] hover:text-[#d4af37]"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="size-4"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="5"
                                />
                                <path
                                    d="M14 20v-6h2l.3-3H14V9.3A1.3 1.3 0 0 1 15.3 8H16V5.3A16 16 0 0 0 14.2 5 3.2 3.2 0 0 0 11 8.4V11H9v3h2v6"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3
                        class="text-xs font-semibold tracking-[0.25em] text-white uppercase"
                    >
                        Navigation
                    </h3>
                    <nav class="mt-6 flex flex-col gap-3">
                        <Link
                            v-for="item in navItems"
                            :key="item.title"
                            :href="item.href"
                            class="text-sm text-[#aab0c2] transition-colors hover:text-[#d4af37]"
                        >
                            {{ item.title }}
                        </Link>
                    </nav>
                </div>

                <div>
                    <h3
                        class="text-xs font-semibold tracking-[0.25em] text-white uppercase"
                    >
                        Products
                    </h3>
                    <nav class="mt-6 flex flex-col gap-3">
                        <Link
                            v-for="item in productItems"
                            :key="item"
                            :href="productsIndex()"
                            class="text-sm text-[#aab0c2] transition-colors hover:text-[#d4af37]"
                        >
                            {{ item }}
                        </Link>
                    </nav>
                </div>

                <div>
                    <h3
                        class="text-xs font-semibold tracking-[0.25em] text-white uppercase"
                    >
                        Contact / Export Team
                    </h3>
                    <ul class="mt-6 flex flex-col gap-3 text-sm">
                        <li class="flex items-center gap-3">
                            <Mail class="size-4 shrink-0 text-[#b99a45]" />
                            <a
                                href="mailto:info@sevaradates.com"
                                class="transition-colors hover:text-[#d4af37]"
                            >
                                info@sevaradates.com
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <Phone class="size-4 shrink-0 text-[#b99a45]" />
                            <a
                                href="tel:+971581809792"
                                class="transition-colors hover:text-[#d4af37]"
                            >
                                +971 58 180 9792
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <MapPin class="size-4 shrink-0 text-[#b99a45]" />
                            <span>Dubai, United Arab Emirates</span>
                        </li>
                    </ul>

                    <Link
                        :href="contact()"
                        class="mt-6 inline-flex items-center bg-primeGold px-6 py-3 text-sm font-medium tracking-wide text-[#0f1c3f] transition hover:bg-[#c9a233]"
                    >
                        Contact Export Team
                    </Link>
                </div>
            </div>

            <div
                class="mt-16 flex flex-col items-center gap-4 border-t border-white/10 pt-8 sm:flex-row sm:justify-between"
            >
                <p class="text-xs text-[#aab0c2]">
                    &copy; {{ currentYear }} Sevara Dates. All rights reserved.
                </p>
                <div class="flex items-center gap-6 text-xs">
                    <a href="#" class="transition-colors hover:text-[#d4af37]">
                        Privacy Policy
                    </a>
                    <a href="#" class="transition-colors hover:text-[#d4af37]">
                        Terms &amp; Conditions
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
