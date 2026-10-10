<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { PackageCheck, Send } from '@lucide/vue';
import SampleRequestController from '@/actions/App/Http/Controllers/SampleRequestController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Product } from '@/types';

const open = defineModel<boolean>('open', { required: true });

defineProps<{
    product: Product;
    sampleVolumes: string[];
    shippingTerms: string[];
}>();

const fieldClass =
    'min-h-11 w-full rounded-sm border border-[#d9d2c3] bg-white px-3 py-2.5 text-sm text-[#0f1c3f] outline-none transition placeholder:text-[#9a9386] focus:border-[#d4af37] focus:ring-2 focus:ring-[#d4af37]/20';

function closeOnSuccess(): void {
    open.value = false;
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-2xl border-[#d4af37]/30 bg-[#fdfbf6] p-0 shadow-[0_28px_80px_-32px_rgba(15,28,63,0.5)] sm:max-w-xl"
        >
            <Form
                v-bind="SampleRequestController.form(product.slug)"
                :options="{ preserveScroll: true }"
                reset-on-success
                class="p-6 sm:p-8"
                @success="closeOnSuccess"
                v-slot="{ errors, processing }"
            >
                <DialogHeader class="text-left">
                    <span
                        class="flex size-11 items-center justify-center rounded-full bg-[#d4af37]/15 text-[#a8842b]"
                    >
                        <PackageCheck class="size-5" />
                    </span>
                    <DialogTitle
                        class="mt-3 font-serif text-3xl text-[#0f1c3f]"
                    >
                        Request a sample
                    </DialogTitle>
                    <DialogDescription class="text-sm leading-6 text-[#6b6558]">
                        Tell us what you need. Our export team will follow up
                        with sample availability and shipping details.
                    </DialogDescription>
                </DialogHeader>

                <div
                    class="mt-6 rounded-sm border border-[#d4af37]/30 bg-[#f7f0df] px-4 py-3"
                >
                    <p
                        class="text-[11px] font-semibold tracking-[0.14em] text-[#8c702c] uppercase"
                    >
                        Selected product
                    </p>
                    <p class="mt-1 font-serif text-lg text-[#0f1c3f]">
                        {{ product.seo_title }}
                    </p>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            for="sample-full-name"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Full name <span aria-hidden="true">*</span>
                        </label>
                        <input
                            id="sample-full-name"
                            name="full_name"
                            type="text"
                            required
                            autocomplete="name"
                            maxlength="255"
                            :aria-invalid="Boolean(errors.full_name)"
                            :class="fieldClass"
                        />
                        <InputError :message="errors.full_name" />
                    </div>
                    <div>
                        <label
                            for="sample-email"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Email address <span aria-hidden="true">*</span>
                        </label>
                        <input
                            id="sample-email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            maxlength="255"
                            :aria-invalid="Boolean(errors.email)"
                            :class="fieldClass"
                        />
                        <InputError :message="errors.email" />
                    </div>
                    <div>
                        <label
                            for="sample-company"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Company <span class="font-normal">(optional)</span>
                        </label>
                        <input
                            id="sample-company"
                            name="company_name"
                            type="text"
                            autocomplete="organization"
                            maxlength="255"
                            :aria-invalid="Boolean(errors.company_name)"
                            :class="fieldClass"
                        />
                        <InputError :message="errors.company_name" />
                    </div>
                    <div>
                        <label
                            for="sample-phone"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Phone <span class="font-normal">(optional)</span>
                        </label>
                        <input
                            id="sample-phone"
                            name="phone"
                            type="tel"
                            autocomplete="tel"
                            maxlength="50"
                            :aria-invalid="Boolean(errors.phone)"
                            :class="fieldClass"
                        />
                        <InputError :message="errors.phone" />
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            for="sample-volume"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Sample volume <span aria-hidden="true">*</span>
                        </label>
                        <select
                            id="sample-volume"
                            name="sample_volume"
                            required
                            :aria-invalid="Boolean(errors.sample_volume)"
                            :class="fieldClass"
                        >
                            <option value="" disabled selected>
                                Select a volume
                            </option>
                            <option
                                v-for="volume in sampleVolumes"
                                :key="volume"
                                :value="volume"
                            >
                                {{ volume }}
                            </option>
                        </select>
                        <InputError :message="errors.sample_volume" />
                    </div>
                    <fieldset class="sm:col-span-2">
                        <legend
                            class="mb-2 text-xs font-semibold text-[#3a3a35]"
                        >
                            Shipping terms <span aria-hidden="true">*</span>
                        </legend>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                v-for="term in shippingTerms"
                                :key="term"
                                class="cursor-pointer"
                            >
                                <input
                                    type="radio"
                                    name="shipping_term"
                                    :value="term"
                                    required
                                    class="peer sr-only"
                                />
                                <span
                                    class="block rounded-sm border border-[#d9d2c3] bg-white px-4 py-3 text-sm font-semibold text-[#0f1c3f] transition peer-checked:border-[#b28b2d] peer-checked:bg-[#f7f0df] peer-focus-visible:ring-2 peer-focus-visible:ring-[#d4af37]"
                                >
                                    {{ term }}
                                    <span
                                        class="mt-0.5 block text-xs font-normal text-[#6b6558]"
                                    >
                                        {{
                                            term === 'FOB'
                                                ? 'From origin port'
                                                : 'To your destination'
                                        }}
                                    </span>
                                </span>
                            </label>
                        </div>
                        <InputError :message="errors.shipping_term" />
                    </fieldset>
                    <div class="sm:col-span-2">
                        <label
                            for="sample-description"
                            class="mb-1.5 block text-xs font-semibold text-[#3a3a35]"
                        >
                            Requirements
                            <span class="font-normal">(optional)</span>
                        </label>
                        <textarea
                            id="sample-description"
                            name="description"
                            rows="3"
                            maxlength="2000"
                            placeholder="Destination, timing, packaging, or anything else we should know"
                            :aria-invalid="Boolean(errors.description)"
                            :class="fieldClass"
                        />
                        <InputError :message="errors.description" />
                    </div>
                </div>

                <Button
                    type="submit"
                    :disabled="processing"
                    class="mt-6 h-12 w-full bg-[#0f1c3f] text-xs font-semibold tracking-[0.16em] text-lightGold uppercase hover:bg-[#162851]"
                >
                    <Send class="size-4" />
                    {{ processing ? 'Sending…' : 'Send sample request' }}
                </Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
