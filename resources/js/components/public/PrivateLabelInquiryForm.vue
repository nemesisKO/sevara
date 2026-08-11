<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Mail, Package } from '@lucide/vue';
import PrivateLabelInquiryController from '@/actions/App/Http/Controllers/PrivateLabelInquiryController';
import LazyImage from '@/components/LazyImage.vue';
import type { PackagingFormat } from '@/types';

withDefaults(
    defineProps<{
        markets: string[];
        formats: PackagingFormat[];
        sectionId?: string;
        heading?: string;
        description?: string;
        submitLabel?: string;
        panelLabel?: string;
        panelImage?: string;
    }>(),
    {
        sectionId: 'private-label-inquiry',
        heading: 'Tell Us About Your Private Label Project',
        description:
            'Share your market, packaging needs and target quantity. Our export team will review and contact you.',
        submitLabel: 'Send Private Label Inquiry',
        panelLabel: 'Private Label Kit',
    },
);

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const fieldClasses =
    'w-full border border-white/15 bg-white/5 px-4 py-3 text-sm text-white transition placeholder:text-[#8e97b0] focus:border-[#d4af37] focus:outline-none';
</script>

<template>
    <section
        :id="sectionId"
        class="relative scroll-mt-8 overflow-hidden bg-[#0f1c3f] py-20"
        :aria-labelledby="`${sectionId}-heading`"
    >
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-24 -left-40 size-128 -rotate-12 opacity-[0.07]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -right-40 -bottom-32 size-112 rotate-18 opacity-[0.06]"
                :style="{ background: mandala }"
            />
        </div>

        <div
            v-if="panelImage"
            class="pointer-events-none absolute inset-y-0 right-0 hidden aspect-square max-w-[58%] lg:block"
        >
            <LazyImage
                :src="panelImage"
                :alt="panelLabel"
                class="h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-[#0f1c3f]/20" />
            <div
                class="absolute inset-0 bg-linear-to-r from-[#0f1c3f] to-transparent to-32%"
            />
        </div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 lg:px-10"
            :class="panelImage ? '' : 'lg:grid-cols-2'"
        >
            <div :class="panelImage ? 'lg:max-w-xl' : ''">
                <h2
                    :id="`${sectionId}-heading`"
                    class="font-serif text-3xl text-[#e9d9ae] sm:text-4xl"
                >
                    {{ heading }}
                </h2>
                <p class="mt-4 max-w-md text-[#9aa5c4]">{{ description }}</p>

                <Form
                    v-bind="PrivateLabelInquiryController.store.form()"
                    reset-on-success
                    class="mt-8 grid max-w-xl gap-4 sm:grid-cols-2"
                    v-slot="{ errors, processing }"
                >
                    <div>
                        <label for="full_name" class="sr-only">Full name</label>
                        <input
                            id="full_name"
                            name="full_name"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Full Name"
                            :class="fieldClasses"
                        />
                        <p
                            v-if="errors.full_name"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.full_name }}
                        </p>
                    </div>

                    <div>
                        <label for="company_name" class="sr-only">
                            Company name
                        </label>
                        <input
                            id="company_name"
                            name="company_name"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Company Name"
                            :class="fieldClasses"
                        />
                        <p
                            v-if="errors.company_name"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.company_name }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="country" class="sr-only">
                            Country / market
                        </label>
                        <select
                            id="country"
                            name="country"
                            required
                            :class="fieldClasses"
                        >
                            <option value="" disabled selected>
                                Country / Market
                            </option>
                            <option
                                v-for="market in markets"
                                :key="market"
                                :value="market"
                                class="text-[#0f1c3f]"
                            >
                                {{ market }}
                            </option>
                        </select>
                        <p
                            v-if="errors.country"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.country }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="product_format" class="sr-only">
                            Product format
                        </label>
                        <select
                            id="product_format"
                            name="product_format"
                            required
                            :class="fieldClasses"
                        >
                            <option value="" disabled selected>
                                Product Format
                            </option>
                            <option
                                v-for="format in formats"
                                :key="format.id"
                                :value="format.name"
                                class="text-[#0f1c3f]"
                            >
                                {{ format.name }}
                            </option>
                            <option value="Not sure yet" class="text-[#0f1c3f]">
                                Not sure yet
                            </option>
                        </select>
                        <p
                            v-if="errors.product_format"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.product_format }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="estimated_quantity" class="sr-only">
                            Estimated quantity / MOQ
                        </label>
                        <input
                            id="estimated_quantity"
                            name="estimated_quantity"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Estimated Quantity / MOQ"
                            :class="fieldClasses"
                        />
                        <p
                            v-if="errors.estimated_quantity"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.estimated_quantity }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="message" class="sr-only">
                            Message / requirements
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            maxlength="2000"
                            placeholder="Message / Requirements"
                            :class="fieldClasses"
                        />
                        <p
                            v-if="errors.message"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ errors.message }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="processing"
                        class="inline-flex w-fit cursor-pointer items-center gap-2 bg-[#d4af37] px-6 py-3 text-sm font-medium tracking-wide text-[#0f1c3f] transition hover:bg-[#c9a233] disabled:cursor-default disabled:opacity-60 sm:col-span-2"
                    >
                        <Mail class="size-4" />
                        {{ processing ? 'Sending…' : submitLabel }}
                    </button>
                </Form>
            </div>

            <div v-if="!panelImage" class="hidden lg:block">
                <div
                    class="relative flex aspect-4/3 items-center justify-center overflow-hidden rounded-lg border border-[#d4af37]/25 bg-white/[0.03]"
                >
                    <div
                        class="pointer-events-none absolute inset-0 opacity-[0.08] grayscale"
                        :style="{ background: mandala }"
                    />
                    <div class="relative flex flex-col items-center gap-4">
                        <Package
                            class="size-16 text-[#d4af37]/50"
                            :stroke-width="0.9"
                        />
                        <p
                            class="font-serif text-2xl tracking-[0.3em] text-white/40"
                        >
                            SEVARA
                        </p>
                        <p
                            class="text-[10px] tracking-[0.3em] text-[#d4af37]/50 uppercase"
                        >
                            {{ panelLabel }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
