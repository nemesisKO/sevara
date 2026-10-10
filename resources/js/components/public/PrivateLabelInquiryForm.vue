<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    Gift,
    Mail,
    MessageCircle,
    Package,
    Users,
} from '@lucide/vue';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import PrivateLabelInquiryController from '@/actions/App/Http/Controllers/PrivateLabelInquiryController';
import CountryMarketInput from '@/components/public/CountryMarketInput.vue';
import type { InquiryProduct, PackagingFormat } from '@/types';

const props = withDefaults(
    defineProps<{
        markets: string[];
        formats: PackagingFormat[];
        products: InquiryProduct[];
        initialIntent?: string | null;
        sourcePage?: string | null;
        sectionId?: string;
        heading?: string;
        description?: string;
        submitLabel?: string;
    }>(),
    {
        sectionId: 'private-label-inquiry',
        initialIntent: null,
        heading: 'Tell Us About Your Private Label Project',
        description:
            'Share your market, packaging needs and target quantity. Our export team will review and contact you.',
        submitLabel: 'Send Private Label Inquiry',
    },
);

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const fieldClasses =
    'w-full border border-white/15 bg-white/5 px-4 py-3 text-sm text-white transition placeholder:text-[#8e97b0] focus:border-[#d4af37] focus:outline-none';

const selectedIntent = ref(props.initialIntent ?? '');

function selectIntent(intent: string): void {
    selectedIntent.value = intent;
}

defineExpose({ selectIntent });

watch(
    () => props.initialIntent,
    (intent) => {
        selectedIntent.value = intent ?? '';
    },
);

const inquiryIntents = [
    {
        value: 'private_label',
        title: 'Private Label',
        description: 'Create a Sevara product tailored to your brand.',
        icon: Package,
    },
    {
        value: 'sample_request',
        title: 'Request Samples',
        description: 'Try our products before your next decision.',
        icon: Gift,
    },
    {
        value: 'distribution_partnership',
        title: 'Distribution Partnership',
        description: 'Bring Sevara to your market.',
        icon: Users,
    },
    {
        value: 'general_export_enquiry',
        title: 'General Export Enquiry',
        description: 'Talk to us about another export opportunity.',
        icon: MessageCircle,
    },
];

const selectedIntentDetails = computed(() =>
    inquiryIntents.find((intent) => intent.value === selectedIntent.value),
);

const isProjectInquiry = computed(() =>
    ['private_label', 'distribution_partnership'].includes(
        selectedIntent.value,
    ),
);

const requiresProduct = computed(
    () => selectedIntent.value !== 'general_export_enquiry',
);

const intentStepContainer = ref<HTMLElement | null>(null);
const intentStepHeight = ref<number | null>(null);
let intentPanelResizeObserver: ResizeObserver | null = null;

function measureIntentPanel(element?: Element): void {
    const panel =
        element instanceof HTMLElement
            ? element
            : intentStepContainer.value?.querySelector<HTMLElement>(
                  '[data-intent-panel]',
              );

    if (!panel) {
        return;
    }

    intentStepHeight.value = Math.ceil(panel.getBoundingClientRect().height);

    intentPanelResizeObserver?.disconnect();
    intentPanelResizeObserver = new ResizeObserver((entries) => {
        const [entry] = entries;

        if (entry) {
            intentStepHeight.value = Math.ceil(entry.contentRect.height);
        }
    });
    intentPanelResizeObserver.observe(panel);
}

function focusIntentForm(element: Element): void {
    measureIntentPanel(element);

    if (
        !selectedIntent.value ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    window.requestAnimationFrame(() => {
        const form = document.getElementById(`${props.sectionId}-form`);

        if (!form) {
            return;
        }

        const targetTop = Math.max(
            0,
            window.scrollY + form.getBoundingClientRect().top - 96,
        );

        if (Math.abs(window.scrollY - targetTop) > 12) {
            window.scrollTo({ top: targetTop, behavior: 'smooth' });
        }
    });
}

onMounted(() => {
    void nextTick(() => measureIntentPanel());
});

onBeforeUnmount(() => {
    intentPanelResizeObserver?.disconnect();
});
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

        <div class="relative mx-auto max-w-3xl px-6 lg:px-10">
            <div>
                <h2
                    :id="`${sectionId}-heading`"
                    class="text-center font-serif text-3xl text-primeGold sm:text-4xl"
                >
                    {{ heading }}
                </h2>
                <p
                    class="mx-auto mt-4 max-w-md text-center font-serif text-[#9aa5c4]"
                >
                    {{ description }}
                </p>

                <Form
                    :id="`${sectionId}-form`"
                    v-bind="PrivateLabelInquiryController.store.form()"
                    reset-on-success
                    :options="{ preserveScroll: true }"
                    class="mx-auto mt-10 grid max-w-2xl scroll-mt-24 gap-5 sm:grid-cols-2"
                    v-slot="{ errors, processing, wasSuccessful, clearErrors }"
                >
                    <input
                        v-if="sourcePage"
                        type="hidden"
                        name="source_page"
                        :value="sourcePage"
                    />
                    <div
                        v-if="wasSuccessful"
                        class="flex min-h-80 animate-in flex-col items-center justify-center border border-emerald-300/30 bg-emerald-400/10 px-6 py-10 text-center text-emerald-100 duration-500 fade-in-0 zoom-in-95 sm:col-span-2"
                        role="status"
                    >
                        <div
                            class="flex size-16 animate-in items-center justify-center rounded-full border border-emerald-300/40 bg-emerald-300/10 text-emerald-200 delay-150 duration-500 zoom-in-50"
                        >
                            <CheckCircle2 class="size-9" />
                        </div>
                        <h3 class="mt-5 font-serif text-2xl text-white">
                            Message received
                        </h3>
                        <p
                            class="mt-2 max-w-sm text-sm leading-6 text-emerald-100/80"
                        >
                            Thank you. Our team will contact you shortly.
                        </p>
                    </div>

                    <nav
                        v-if="!wasSuccessful"
                        aria-label="Enquiry steps"
                        class="sm:col-span-2"
                    >
                        <ol class="grid grid-cols-2 gap-2 sm:gap-3">
                            <li>
                                <button
                                    type="button"
                                    :disabled="!selectedIntent"
                                    :aria-current="
                                        selectedIntent ? undefined : 'step'
                                    "
                                    :class="[
                                        'flex h-full w-full items-start gap-3 border p-3 text-left transition sm:p-4',
                                        selectedIntent
                                            ? 'cursor-pointer border-white/20 bg-white/[0.03] enabled:hover:border-[#d4af37] enabled:hover:bg-white/[0.06]'
                                            : 'cursor-default border-[#d4af37] bg-[#d4af37]/10',
                                    ]"
                                    @click="
                                        selectedIntent = '';
                                        clearErrors();
                                    "
                                >
                                    <CheckCircle2
                                        v-if="selectedIntent"
                                        aria-hidden="true"
                                        class="size-7 shrink-0 text-[#d4af37]"
                                    />
                                    <span
                                        v-else
                                        aria-hidden="true"
                                        class="flex size-7 shrink-0 items-center justify-center rounded-full bg-[#d4af37] text-xs font-bold text-[#0f1c3f]"
                                    >
                                        1
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span
                                            class="block text-[10px] font-semibold tracking-[0.12em] text-[#9aa5c4] uppercase"
                                        >
                                            Step 1 of 2
                                        </span>
                                        <span
                                            class="mt-0.5 block text-sm font-semibold text-white"
                                        >
                                            Choose enquiry
                                        </span>
                                        <span
                                            v-if="selectedIntent"
                                            class="mt-1 block text-xs leading-5 text-[#e3c474]"
                                        >
                                            {{ selectedIntentDetails?.title }}
                                            <span
                                                class="mt-1 inline-flex items-center gap-1 font-semibold text-white underline underline-offset-2"
                                            >
                                                <ArrowLeft
                                                    aria-hidden="true"
                                                    class="size-3"
                                                />
                                                Change enquiry type
                                            </span>
                                        </span>
                                        <span
                                            v-else
                                            class="mt-1 block text-xs text-[#e3c474]"
                                        >
                                            Current step
                                        </span>
                                    </span>
                                </button>
                            </li>
                            <li
                                :aria-current="
                                    selectedIntent ? 'step' : undefined
                                "
                                :class="[
                                    'flex items-start gap-3 border p-3 sm:p-4',
                                    selectedIntent
                                        ? 'border-[#d4af37] bg-[#d4af37]/10'
                                        : 'border-white/15 bg-white/[0.03]',
                                ]"
                            >
                                <span
                                    aria-hidden="true"
                                    :class="[
                                        'flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                        selectedIntent
                                            ? 'bg-[#d4af37] text-[#0f1c3f]'
                                            : 'border border-white/30 text-[#9aa5c4]',
                                    ]"
                                >
                                    2
                                </span>
                                <span class="min-w-0">
                                    <span
                                        class="block text-[10px] font-semibold tracking-[0.12em] text-[#9aa5c4] uppercase"
                                    >
                                        Step 2 of 2
                                    </span>
                                    <span
                                        :class="[
                                            'mt-0.5 block text-sm font-semibold',
                                            selectedIntent
                                                ? 'text-white'
                                                : 'text-[#9aa5c4]',
                                        ]"
                                    >
                                        Your details
                                    </span>
                                    <span
                                        :class="[
                                            'mt-1 block text-xs',
                                            selectedIntent
                                                ? 'text-[#e3c474]'
                                                : 'text-[#9aa5c4]',
                                        ]"
                                    >
                                        {{
                                            selectedIntent
                                                ? 'Current step'
                                                : 'Up next'
                                        }}
                                    </span>
                                </span>
                            </li>
                        </ol>
                    </nav>

                    <div
                        v-if="!wasSuccessful"
                        ref="intentStepContainer"
                        class="relative min-h-[29rem] transition-[height] duration-300 ease-out sm:col-span-2 sm:min-h-[18rem]"
                        :style="
                            intentStepHeight
                                ? { height: `${intentStepHeight}px` }
                                : undefined
                        "
                    >
                        <input
                            name="intent"
                            type="hidden"
                            :value="selectedIntent"
                        />

                        <Transition
                            name="intent-step"
                            mode="out-in"
                            @before-enter="measureIntentPanel"
                            @after-enter="focusIntentForm"
                        >
                            <div
                                v-if="!selectedIntent"
                                key="intent-options"
                                data-intent-panel
                                class="absolute inset-x-0 top-0"
                            >
                                <p
                                    class="text-center text-xs font-semibold tracking-[0.2em] text-[#d4af37] uppercase"
                                >
                                    What can we help you with?
                                </p>
                                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                    <button
                                        v-for="intent in inquiryIntents"
                                        :key="intent.value"
                                        type="button"
                                        :aria-pressed="
                                            selectedIntent === intent.value
                                        "
                                        :class="[
                                            'flex cursor-pointer items-start gap-4 border p-5 text-left transition',
                                            selectedIntent === intent.value
                                                ? 'border-[#d4af37] bg-[#d4af37]/15'
                                                : 'border-white/15 bg-white/[0.03] hover:border-[#d4af37]/60 hover:bg-white/[0.06]',
                                        ]"
                                        @click="selectedIntent = intent.value"
                                    >
                                        <component
                                            :is="intent.icon"
                                            class="mt-0.5 size-6 shrink-0 text-[#d4af37]"
                                        />
                                        <span>
                                            <span
                                                class="block text-base font-semibold text-white"
                                            >
                                                {{ intent.title }}
                                            </span>
                                            <span
                                                class="mt-1 block font-serif text-xs leading-5 text-[#9aa5c4]"
                                            >
                                                {{ intent.description }}
                                            </span>
                                        </span>
                                    </button>
                                </div>
                                <p
                                    v-if="errors.intent"
                                    class="mt-1 text-xs text-red-300"
                                >
                                    {{ errors.intent }}
                                </p>
                            </div>

                            <div
                                v-else
                                :key="selectedIntent"
                                data-intent-panel
                                class="absolute inset-x-0 top-0 grid gap-4 sm:grid-cols-2"
                            >
                                <div class="text-center sm:col-span-2">
                                    <p
                                        class="text-xs font-semibold tracking-[0.2em] text-[#d4af37] uppercase"
                                    >
                                        Your enquiry
                                    </p>
                                    <h3
                                        class="mt-2 font-serif text-2xl text-white sm:text-3xl"
                                    >
                                        {{ selectedIntentDetails?.title }}
                                    </h3>
                                    <p
                                        class="mx-auto mt-2 max-w-md font-serif text-sm text-[#9aa5c4]"
                                    >
                                        {{ selectedIntentDetails?.description }}
                                    </p>
                                </div>
                                <div>
                                    <label for="full_name" class="sr-only"
                                        >Full name</label
                                    >
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
                                    <label for="email" class="sr-only">
                                        Email address
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        required
                                        maxlength="255"
                                        placeholder="Email Address"
                                        :class="fieldClasses"
                                    />
                                    <p
                                        v-if="errors.email"
                                        class="mt-1 text-xs text-red-300"
                                    >
                                        {{ errors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label for="phone" class="sr-only">
                                        Phone number (optional)
                                    </label>
                                    <input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        maxlength="50"
                                        autocomplete="tel"
                                        placeholder="Phone Number (Optional)"
                                        :class="fieldClasses"
                                    />
                                    <p
                                        v-if="errors.phone"
                                        class="mt-1 text-xs text-red-300"
                                    >
                                        {{ errors.phone }}
                                    </p>
                                </div>

                                <div v-if="isProjectInquiry">
                                    <label for="company_name" class="sr-only">
                                        Company name
                                    </label>
                                    <input
                                        id="company_name"
                                        name="company_name"
                                        type="text"
                                        required
                                        maxlength="255"
                                        autocomplete="organization"
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

                                <div
                                    v-if="requiresProduct"
                                    class="sm:col-span-2"
                                >
                                    <label for="product_name" class="sr-only">
                                        Product
                                    </label>
                                    <select
                                        id="product_name"
                                        name="product_name"
                                        required
                                        :class="fieldClasses"
                                    >
                                        <option value="" disabled selected>
                                            Choose a Product
                                        </option>
                                        <option
                                            v-for="product in products"
                                            :key="product.id"
                                            :value="product.name"
                                            class="text-[#0f1c3f]"
                                        >
                                            {{ product.name }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="errors.product_name"
                                        class="mt-1 text-xs text-red-300"
                                    >
                                        {{ errors.product_name }}
                                    </p>
                                </div>

                                <div
                                    v-if="isProjectInquiry"
                                    class="sm:col-span-2"
                                >
                                    <label for="country" class="sr-only">
                                        Country / market
                                    </label>
                                    <CountryMarketInput
                                        :markets="markets"
                                        :field-classes="fieldClasses"
                                    />
                                    <p
                                        v-if="errors.country"
                                        class="mt-1 text-xs text-red-300"
                                    >
                                        {{ errors.country }}
                                    </p>
                                </div>

                                <div
                                    v-if="isProjectInquiry"
                                    class="sm:col-span-2"
                                >
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
                                        <option
                                            value="Not sure yet"
                                            class="text-[#0f1c3f]"
                                        >
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

                                <div
                                    v-if="isProjectInquiry"
                                    class="sm:col-span-2"
                                >
                                    <label
                                        for="estimated_quantity"
                                        class="sr-only"
                                    >
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
                                    class="inline-flex w-fit cursor-pointer items-center gap-2 bg-primeGold px-6 py-3 text-sm font-medium tracking-wide text-[#0f1c3f] transition hover:bg-[#c9a233] disabled:cursor-default disabled:opacity-60 sm:col-span-2"
                                >
                                    <Mail class="size-4" />
                                    {{ processing ? 'Sending…' : submitLabel }}
                                </button>
                            </div>
                        </Transition>
                    </div>
                </Form>
            </div>
        </div>
    </section>
</template>

<style scoped>
.intent-step-enter-active,
.intent-step-leave-active {
    transition:
        opacity 250ms ease,
        transform 250ms ease;
}

.intent-step-enter-from,
.intent-step-leave-to {
    opacity: 0;
    transform: translateY(-0.5rem);
}
</style>
