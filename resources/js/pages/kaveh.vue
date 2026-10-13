<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, CheckCircle2, Plus, Trash2 } from '@lucide/vue';
import { computed, nextTick, ref } from 'vue';
import PrivateLabelProjectController from '@/actions/App/Http/Controllers/PrivateLabelProjectController';

type Sku = { product: string; pack_size: string; quantity: string };
type FileField =
    'product_files' | 'brand_files' | 'packaging_files' | 'compliance_files';

type ProjectForm = {
    full_name: string;
    job_title: string;
    company_name: string;
    email: string;
    phone: string;
    website: string;
    company_country: string;
    buyer_type: string[];
    target_markets: string;
    sales_channel: string[];
    project_type: string;
    positioning: string;
    development_type: string;
    product_interest: string;
    chocolate_type: string;
    filling: string;
    ingredient_requirements: string;
    target_shelf_life: string;
    packaging_format: string;
    net_weight: string;
    skus: Sku[];
    artwork_status: string;
    has_compliance_requirements: string;
    certifications: string[];
    label_languages: string;
    technical_requirements: string;
    initial_order: string;
    order_unit: string;
    forecast: string;
    target_price: string;
    target_currency: string;
    delivery_country: string;
    delivery_location: string;
    incoterm: string;
    launch_date: string;
    required_delivery_date: string;
    sample_request: string;
    decision_makers: string[];
    comments: string;
    product_files: File[];
    brand_files: File[];
    packaging_files: File[];
    compliance_files: File[];
    accuracy_confirmation: boolean;
    privacy_consent: boolean;
};

type Prefill = Pick<
    ProjectForm,
    | 'full_name'
    | 'company_name'
    | 'email'
    | 'phone'
    | 'company_country'
    | 'target_markets'
    | 'product_interest'
    | 'packaging_format'
    | 'delivery_country'
    | 'comments'
>;

const props = defineProps<{
    projectReference: string | null;
    projectToken: string;
    prefill: Prefill;
}>();
const steps = [
    'Business',
    'Market',
    'Product',
    'Packaging',
    'Compliance',
    'Commercial',
    'Timeline',
    'Review',
];
const buyerTypes = [
    ['Importer / Distributor', 'Importer / Distributor'],
    ['Retailer', 'Retailer'],
    ['Private Label Brand', 'Private Label Brand'],
    ['E-commerce Brand', 'E-commerce Brand'],
    ['Retail Chain', 'Supermarket / Retail Chain'],
    ['Corporate Gifting', 'Corporate Gifting'],
    ['HORECA', 'HORECA'],
    ['Wholesaler', 'Wholesaler'],
];
const salesChannels = [
    ['Supermarkets', 'Supermarkets'],
    ['Gourmet Retail', 'Specialty / Gourmet Retail'],
    ['E-commerce', 'E-commerce'],
    ['Wholesale', 'Distributor / Wholesale'],
    ['Corporate Gifting', 'Corporate Gifting'],
    ['HORECA', 'HORECA'],
    ['Duty Free', 'Duty Free / Travel Retail'],
];
const certifications = [
    ['Halal', 'Halal'],
    ['HACCP', 'HACCP / Food Safety'],
    ['ISO', 'ISO requirement'],
    ['Organic', 'Organic'],
    ['Vegan', 'Vegan'],
    ['Retailer Standard', 'Retailer-specific standard'],
    ['Lab Testing', 'Laboratory testing'],
    ['Market Registration', 'Market registration'],
];
const decisionMakers = [
    ['Myself', 'I am the decision-maker'],
    ['Management', 'Management'],
    ['Procurement', 'Procurement'],
    ['Product Team', 'Product / Category Team'],
    ['Marketing', 'Marketing / Brand'],
    ['Quality', 'Quality / Regulatory'],
];

const form = useForm<ProjectForm>({
    full_name: props.prefill.full_name,
    job_title: '',
    company_name: props.prefill.company_name,
    email: props.prefill.email,
    phone: props.prefill.phone,
    website: '',
    company_country: props.prefill.company_country,
    buyer_type: [],
    target_markets: props.prefill.target_markets,
    sales_channel: [],
    project_type: '',
    positioning: '',
    development_type: '',
    product_interest: props.prefill.product_interest,
    chocolate_type: '',
    filling: '',
    ingredient_requirements: '',
    target_shelf_life: '',
    packaging_format: props.prefill.packaging_format,
    net_weight: '',
    skus: [{ product: '', pack_size: '', quantity: '' }],
    artwork_status: '',
    has_compliance_requirements: '',
    certifications: [],
    label_languages: '',
    technical_requirements: '',
    initial_order: '',
    order_unit: 'MT',
    forecast: '',
    target_price: '',
    target_currency: '',
    delivery_country: props.prefill.delivery_country,
    delivery_location: '',
    incoterm: '',
    launch_date: '',
    required_delivery_date: '',
    sample_request: '',
    decision_makers: [],
    comments: props.prefill.comments,
    product_files: [],
    brand_files: [],
    packaging_files: [],
    compliance_files: [],
    accuracy_confirmation: false,
    privacy_consent: false,
});

const currentStep = ref(0);
const wizard = ref<HTMLElement | null>(null);
const uploadError = ref('');
const successReference = ref(props.projectReference);
const progress = computed(() =>
    Math.round((Math.min(currentStep.value + 1, 7) / 7) * 100),
);
const showCustomProduct = computed(() =>
    ['modify', 'custom'].includes(form.development_type),
);
const showCompliance = computed(
    () => form.has_compliance_requirements === 'yes',
);

function fieldError(name: string): string | undefined {
    return (form.errors as Record<string, string>)[name];
}

function display(value: string | string[]): string {
    if (Array.isArray(value)) {
        return value.length ? value.join(', ') : '—';
    }

    return value || '—';
}

const reviewGroups = computed(() => [
    {
        title: 'Business',
        rows: [
            ['Company', form.company_name],
            ['Contact', form.full_name],
            ['Job title', form.job_title],
            ['Email', form.email],
            ['Company country', form.company_country],
            ['Buyer type', display(form.buyer_type)],
        ],
    },
    {
        title: 'Market & product',
        rows: [
            ['Target markets', form.target_markets],
            ['Sales channels', display(form.sales_channel)],
            ['Project type', form.project_type],
            ['Product interest', form.product_interest],
            ['Development type', form.development_type],
        ],
    },
    {
        title: 'Packaging & commercial',
        rows: [
            ['Packaging', form.packaging_format],
            ['Pack weight', form.net_weight],
            [
                'SKUs',
                form.skus
                    .filter(
                        (sku) => sku.product || sku.pack_size || sku.quantity,
                    )
                    .map((sku) =>
                        [sku.product, sku.pack_size, sku.quantity]
                            .filter(Boolean)
                            .join(' / '),
                    )
                    .join('; '),
            ],
            [
                'Initial order',
                form.initial_order
                    ? form.initial_order + ' ' + form.order_unit
                    : '',
            ],
            ['Delivery country', form.delivery_country],
            ['Delivery location', form.delivery_location],
            ['Incoterm', form.incoterm],
        ],
    },
    {
        title: 'Timeline & files',
        rows: [
            ['Launch date', form.launch_date],
            ['Required delivery', form.required_delivery_date],
            ['Samples', form.sample_request],
            [
                'Uploaded files',
                String(
                    form.product_files.length +
                        form.brand_files.length +
                        form.packaging_files.length +
                        form.compliance_files.length,
                ),
            ],
        ],
    },
]);

function scrollToForm(): void {
    void nextTick(() =>
        wizard.value?.scrollIntoView({ behavior: 'smooth', block: 'start' }),
    );
}

function validateCurrentStep(): boolean {
    form.clearErrors();
    uploadError.value = '';

    if (currentStep.value === 0 && form.buyer_type.length === 0) {
        form.setError('buyer_type', 'Choose at least one buyer type.');

        return false;
    }

    if (currentStep.value === 1 && form.sales_channel.length === 0) {
        form.setError('sales_channel', 'Choose at least one sales channel.');

        return false;
    }

    const panel = wizard.value?.querySelector<HTMLElement>(
        '[data-step="' + currentStep.value + '"]',
    );
    const requiredFields = panel?.querySelectorAll<
        HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement
    >('[required]');

    for (const field of requiredFields ?? []) {
        if (!field.checkValidity()) {
            field.reportValidity();
            field.focus();

            return false;
        }
    }

    return true;
}

function nextStep(): void {
    if (!validateCurrentStep()) {
        return;
    }

    currentStep.value = Math.min(currentStep.value + 1, steps.length - 1);
    scrollToForm();
}

function previousStep(): void {
    currentStep.value = Math.max(currentStep.value - 1, 0);
    scrollToForm();
}

function addSku(): void {
    if (form.skus.length < 20) {
        form.skus.push({ product: '', pack_size: '', quantity: '' });
    }
}

function setFiles(field: FileField, event: Event): void {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    uploadError.value = '';

    if (
        files.length > 10 ||
        files.some((file) => file.size > 25 * 1024 * 1024)
    ) {
        uploadError.value =
            'Select up to 10 files per group, each no larger than 25 MB.';
        input.value = '';

        return;
    }

    form[field] = files;
}

function stepForField(field: string): number {
    const name = field.split('.')[0];

    if (
        [
            'full_name',
            'job_title',
            'company_name',
            'email',
            'phone',
            'website',
            'company_country',
            'buyer_type',
        ].includes(name)
    ) {
        return 0;
    }

    if (
        [
            'target_markets',
            'sales_channel',
            'project_type',
            'positioning',
        ].includes(name)
    ) {
        return 1;
    }

    if (
        [
            'development_type',
            'product_interest',
            'chocolate_type',
            'filling',
            'ingredient_requirements',
            'target_shelf_life',
            'product_files',
        ].includes(name)
    ) {
        return 2;
    }

    if (
        [
            'packaging_format',
            'net_weight',
            'skus',
            'artwork_status',
            'brand_files',
            'packaging_files',
        ].includes(name)
    ) {
        return 3;
    }

    if (
        [
            'has_compliance_requirements',
            'certifications',
            'label_languages',
            'technical_requirements',
            'compliance_files',
        ].includes(name)
    ) {
        return 4;
    }

    if (
        [
            'initial_order',
            'order_unit',
            'forecast',
            'target_price',
            'target_currency',
            'delivery_country',
            'delivery_location',
            'incoterm',
        ].includes(name)
    ) {
        return 5;
    }

    if (
        [
            'launch_date',
            'required_delivery_date',
            'sample_request',
            'decision_makers',
            'comments',
        ].includes(name)
    ) {
        return 6;
    }

    return 7;
}

function submitProject(): void {
    if (currentStep.value !== 7 || !validateCurrentStep()) {
        return;
    }

    form.post(PrivateLabelProjectController.store.url(props.projectToken), {
        forceFormData: true,
        preserveScroll: true,
        onError: (errors) => {
            const firstField = Object.keys(errors)[0];
            currentStep.value = firstField ? stepForField(firstField) : 7;
            scrollToForm();
        },
        onSuccess: (page) => {
            const reference = page.props.projectReference;
            successReference.value =
                typeof reference === 'string' ? reference : null;
            scrollToForm();
        },
    });
}
</script>

<template>
    <Head title="Start Your Private Label Project">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <section ref="wizard" class="project-page scroll-mt-24 px-5 py-14 sm:py-20">
        <div class="mx-auto max-w-4xl">
            <header class="mb-8 text-center">
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-[#b99a45] uppercase"
                >
                    Private Label
                </p>
                <h1 class="mt-3 font-serif text-4xl text-[#0f1c3f] sm:text-5xl">
                    Start Your Private Label Project
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-[#625d52]">
                    Tell us about your product, packaging and commercial
                    requirements. We’ll review feasibility and recommend the
                    next step.
                </p>
            </header>

            <div
                v-if="successReference"
                class="rounded-2xl border border-[#d4af37]/35 bg-white p-8 text-center shadow-sm sm:p-12"
                role="status"
            >
                <CheckCircle2 class="mx-auto size-12 text-[#b99a45]" />
                <p
                    class="mt-5 text-xs font-bold tracking-[0.2em] text-[#b99a45] uppercase"
                >
                    Project received
                </p>
                <h2 class="mt-2 font-serif text-3xl text-[#0f1c3f]">
                    Thank you.
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-[#625d52]">
                    Our team will review your product, packaging, quantity and
                    market requirements.
                </p>
                <p class="mt-6 font-semibold text-[#0f1c3f]">
                    Project reference: {{ successReference }}
                </p>
                <div
                    class="mx-auto mt-8 max-w-md text-left text-sm text-[#625d52]"
                >
                    <h3 class="font-semibold text-[#0f1c3f]">
                        What happens next
                    </h3>
                    <ol class="mt-2 list-decimal space-y-1 pl-5">
                        <li>Project review</li>
                        <li>Feasibility and requirement clarification</li>
                        <li>Product and packaging confirmation</li>
                        <li>Quotation or sample discussion</li>
                    </ol>
                </div>
            </div>

            <form
                v-else
                class="project-form rounded-2xl border border-[#e7e1d4] bg-white p-5 shadow-sm sm:p-8"
                novalidate
                @submit.prevent="submitProject"
            >
                <div class="mb-8">
                    <div
                        class="flex items-center justify-between text-xs font-semibold tracking-wide text-[#0f1c3f]"
                    >
                        <span>{{
                            currentStep === 7
                                ? 'Review'
                                : 'Step ' +
                                  (currentStep + 1) +
                                  ' of 7 · ' +
                                  steps[currentStep]
                        }}</span>
                        <span>{{ progress }}%</span>
                    </div>
                    <div
                        class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#e7e1d4]"
                        role="progressbar"
                        :aria-valuenow="progress"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-label="Project form progress"
                    >
                        <div
                            class="h-full bg-[#b99a45] transition-all"
                            :style="{ width: progress + '%' }"
                        />
                    </div>
                </div>

                <div
                    v-if="Object.keys(form.errors).length"
                    class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"
                    role="alert"
                >
                    Please review the highlighted information before continuing.
                    <ul class="mt-2 list-disc pl-5">
                        <li v-for="(message, name) in form.errors" :key="name">
                            {{ message }}
                        </li>
                    </ul>
                </div>
                <p
                    v-if="uploadError"
                    class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800"
                    role="alert"
                >
                    {{ uploadError }}
                </p>

                <section
                    v-show="currentStep === 0"
                    data-step="0"
                    class="form-step"
                >
                    <h2>Tell us about your business</h2>
                    <div class="field-grid">
                        <label
                            >Full Name *<input
                                v-model="form.full_name"
                                type="text"
                                autocomplete="name"
                                required
                        /></label>
                        <label
                            >Job Title / Role *<input
                                v-model="form.job_title"
                                type="text"
                                required
                        /></label>
                        <label
                            >Company / Brand Name *<input
                                v-model="form.company_name"
                                type="text"
                                required
                        /></label>
                        <label
                            >Business Email *<input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                        /></label>
                        <label
                            >Phone / WhatsApp<input
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                        /></label>
                        <label
                            >Website / LinkedIn<input
                                v-model="form.website"
                                type="url"
                                placeholder="https://..."
                        /></label>
                    </div>
                    <label
                        >Company Country *<input
                            v-model="form.company_country"
                            type="text"
                            autocomplete="country-name"
                            required
                    /></label>
                    <fieldset>
                        <legend>Buyer Type *</legend>
                        <div class="check-grid">
                            <label
                                v-for="[value, label] in buyerTypes"
                                :key="value"
                                class="check-option"
                            >
                                <input
                                    v-model="form.buyer_type"
                                    type="checkbox"
                                    :value="value"
                                />
                                {{ label }}
                            </label>
                        </div>
                        <p v-if="fieldError('buyer_type')" class="field-error">
                            {{ fieldError('buyer_type') }}
                        </p>
                    </fieldset>
                </section>

                <section
                    v-show="currentStep === 1"
                    data-step="1"
                    class="form-step"
                >
                    <h2>Where will you sell the product?</h2>
                    <label
                        >Target Market(s) *
                        <input
                            v-model="form.target_markets"
                            type="text"
                            placeholder="e.g. UAE, Uzbekistan, Saudi Arabia"
                            required
                        />
                    </label>
                    <fieldset>
                        <legend>Sales Channels *</legend>
                        <div class="check-grid">
                            <label
                                v-for="[value, label] in salesChannels"
                                :key="value"
                                class="check-option"
                            >
                                <input
                                    v-model="form.sales_channel"
                                    type="checkbox"
                                    :value="value"
                                />
                                {{ label }}
                            </label>
                        </div>
                        <p
                            v-if="fieldError('sales_channel')"
                            class="field-error"
                        >
                            {{ fieldError('sales_channel') }}
                        </p>
                    </fieldset>
                    <div class="field-grid">
                        <label
                            >Project Type *
                            <select v-model="form.project_type" required>
                                <option value="">Select...</option>
                                <option>Launching a new brand</option>
                                <option>
                                    Adding a product to an existing brand
                                </option>
                                <option>Replacing an existing supplier</option>
                                <option>Retailer private label</option>
                                <option>Testing a new market</option>
                                <option>Other</option>
                            </select>
                        </label>
                        <label
                            >Product Positioning
                            <select v-model="form.positioning">
                                <option value="">Select...</option>
                                <option>Mainstream</option>
                                <option>Premium</option>
                                <option>Affordable Luxury</option>
                                <option>Luxury / Gifting</option>
                                <option>Health-oriented</option>
                                <option>Not decided yet</option>
                            </select>
                        </label>
                    </div>
                </section>

                <section
                    v-show="currentStep === 2"
                    data-step="2"
                    class="form-step"
                >
                    <h2>What would you like us to produce?</h2>
                    <label
                        >Development Type *
                        <select v-model="form.development_type" required>
                            <option value="">Select...</option>
                            <option value="existing">
                                Use an existing product under my brand
                            </option>
                            <option value="modify">
                                Modify an existing product
                            </option>
                            <option value="custom">
                                Develop something different
                            </option>
                            <option value="recommend">
                                I’m not sure — please recommend
                            </option>
                        </select>
                    </label>
                    <label
                        >Product / Flavour Interest *
                        <textarea
                            v-model="form.product_interest"
                            placeholder="Example: Chocolate dates with almond, strawberry chocolate dates..."
                            required
                        />
                    </label>
                    <div v-if="showCustomProduct" class="sub-panel">
                        <div class="field-grid">
                            <label
                                >Preferred Chocolate Type
                                <select v-model="form.chocolate_type">
                                    <option value="">No preference</option>
                                    <option>Dark Chocolate</option>
                                    <option>Milk Chocolate</option>
                                    <option>White Chocolate</option>
                                    <option>Combination</option>
                                </select>
                            </label>
                            <label
                                >Preferred Filling
                                <input
                                    v-model="form.filling"
                                    type="text"
                                    placeholder="e.g. Almond"
                                />
                            </label>
                        </div>
                        <label
                            >Ingredient / Product Restrictions
                            <textarea
                                v-model="form.ingredient_requirements"
                                placeholder="Allergens, sugar requirements, preservatives, ingredients to avoid..."
                            />
                        </label>
                    </div>
                    <label
                        >Target Shelf Life
                        <select v-model="form.target_shelf_life">
                            <option value="">Please recommend</option>
                            <option>6 months</option>
                            <option>9 months</option>
                            <option>12 months</option>
                            <option>Other</option>
                        </select>
                    </label>
                    <div class="upload-box">
                        <strong>Product / Reference Files</strong>
                        <p>
                            Upload specifications, competitor references,
                            product photos or ingredient briefs.
                        </p>
                        <input
                            type="file"
                            multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp"
                            @change="setFiles('product_files', $event)"
                        />
                        <small v-if="form.product_files.length"
                            >{{ form.product_files.length }} file(s)
                            selected</small
                        >
                    </div>
                </section>

                <section
                    v-show="currentStep === 3"
                    data-step="3"
                    class="form-step"
                >
                    <h2>Packaging & Branding</h2>
                    <div class="field-grid">
                        <label
                            >Packaging Format *
                            <select v-model="form.packaging_format" required>
                                <option value="">Select...</option>
                                <option
                                    v-if="
                                        props.prefill.packaging_format &&
                                        ![
                                            'Pillow Pack',
                                            'Stand-up Pouch',
                                            'Not sure — please recommend',
                                        ].includes(
                                            props.prefill.packaging_format,
                                        )
                                    "
                                    :value="props.prefill.packaging_format"
                                >
                                    {{ props.prefill.packaging_format }}
                                </option>
                                <option>Pillow Pack</option>
                                <option>Stand-up Pouch</option>
                                <option>Not sure — please recommend</option>
                            </select>
                        </label>
                        <label
                            >Preferred Net Weight *
                            <select v-model="form.net_weight" required>
                                <option value="">Select...</option>
                                <option>125 g</option>
                                <option>250 g</option>
                                <option>Other</option>
                                <option>Not sure</option>
                            </select>
                        </label>
                    </div>
                    <div
                        class="my-6 flex flex-wrap items-center justify-between gap-3 border-t border-[#e7e1d4] pt-6"
                    >
                        <div>
                            <h3 class="font-serif text-xl text-[#0f1c3f]">
                                Products / SKUs
                            </h3>
                            <p class="text-sm text-[#625d52]">
                                Add each flavour, size or packaging variant
                                separately.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="secondary-btn"
                            :disabled="form.skus.length >= 20"
                            @click="addSku"
                        >
                            <Plus class="size-4" /> Add SKU
                        </button>
                    </div>
                    <div
                        v-for="(sku, index) in form.skus"
                        :key="index"
                        class="sku-row"
                    >
                        <label
                            >Product / flavour<input
                                v-model="sku.product"
                                type="text"
                        /></label>
                        <label
                            >Pack size<input
                                v-model="sku.pack_size"
                                type="text"
                        /></label>
                        <label
                            >Quantity<input v-model="sku.quantity" type="text"
                        /></label>
                        <button
                            type="button"
                            class="remove-btn"
                            :aria-label="'Remove SKU ' + (index + 1)"
                            @click="form.skus.splice(index, 1)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                    <label
                        >Artwork Status *
                        <select v-model="form.artwork_status" required>
                            <option value="">Select...</option>
                            <option>Print-ready artwork available</option>
                            <option>Artwork needs adaptation</option>
                            <option>Logo / brand assets only</option>
                            <option>No artwork yet</option>
                            <option>I need design support</option>
                        </select>
                    </label>
                    <div class="field-grid">
                        <div class="upload-box">
                            <strong>Brand Assets</strong>
                            <p>
                                Logo, brand guidelines and visual identity
                                files.
                            </p>
                            <input
                                type="file"
                                multiple
                                accept=".pdf,.ai,.eps,.svg,.psd,.zip,.jpg,.jpeg,.png"
                                @change="setFiles('brand_files', $event)"
                            />
                            <small v-if="form.brand_files.length"
                                >{{ form.brand_files.length }} file(s)
                                selected</small
                            >
                        </div>
                        <div class="upload-box">
                            <strong>Packaging Artwork / Dielines</strong>
                            <p>
                                Existing artwork, dielines or packaging
                                references.
                            </p>
                            <input
                                type="file"
                                multiple
                                accept=".pdf,.ai,.eps,.svg,.psd,.zip,.jpg,.jpeg,.png"
                                @change="setFiles('packaging_files', $event)"
                            />
                            <small v-if="form.packaging_files.length"
                                >{{ form.packaging_files.length }} file(s)
                                selected</small
                            >
                        </div>
                    </div>
                </section>

                <section
                    v-show="currentStep === 4"
                    data-step="4"
                    class="form-step"
                >
                    <h2>Regulatory & Quality Requirements</h2>
                    <label
                        >Do you have specific regulatory, certification or
                        retailer requirements? *
                        <select
                            v-model="form.has_compliance_requirements"
                            required
                        >
                            <option value="">Select...</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                            <option value="unknown">Not sure</option>
                        </select>
                    </label>
                    <div v-if="showCompliance" class="sub-panel">
                        <fieldset>
                            <legend>Requirements</legend>
                            <div class="check-grid">
                                <label
                                    v-for="[value, label] in certifications"
                                    :key="value"
                                    class="check-option"
                                >
                                    <input
                                        v-model="form.certifications"
                                        type="checkbox"
                                        :value="value"
                                    />
                                    {{ label }}
                                </label>
                            </div>
                        </fieldset>
                        <label
                            >Required Label Language(s)
                            <input
                                v-model="form.label_languages"
                                type="text"
                                placeholder="e.g. English, Arabic, Russian"
                            />
                        </label>
                        <label
                            >Additional Technical Requirements
                            <textarea
                                v-model="form.technical_requirements"
                                placeholder="Retailer requirements, microbiological limits, ingredient restrictions..."
                            />
                        </label>
                        <div class="upload-box">
                            <strong>Compliance / Technical Documents</strong>
                            <p>
                                Upload retailer manuals, regulatory documents,
                                specifications or certification requirements.
                            </p>
                            <input
                                type="file"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @change="setFiles('compliance_files', $event)"
                            />
                            <small v-if="form.compliance_files.length"
                                >{{ form.compliance_files.length }} file(s)
                                selected</small
                            >
                        </div>
                    </div>
                </section>

                <section
                    v-show="currentStep === 5"
                    data-step="5"
                    class="form-step"
                >
                    <h2>Commercial & Delivery Requirements</h2>
                    <div class="field-grid">
                        <label
                            >Estimated Initial Order *
                            <input
                                v-model="form.initial_order"
                                type="number"
                                min="0.1"
                                step="0.1"
                                required
                            />
                        </label>
                        <label
                            >Unit *
                            <select v-model="form.order_unit" required>
                                <option value="MT">MT</option>
                                <option value="kg">kg</option>
                                <option value="cartons">Cartons</option>
                            </select>
                        </label>
                    </div>
                    <p class="info-box">
                        Standard private-label MOQ currently starts from 5 MT.
                        Projects below this level may still be submitted for
                        feasibility review.
                    </p>
                    <label
                        >Expected Repeat Volume / Annual Forecast
                        <input
                            v-model="form.forecast"
                            type="text"
                            placeholder="e.g. 5 MT quarterly / 30 MT annually"
                        />
                    </label>
                    <div class="field-grid">
                        <label
                            >Target Price<input
                                v-model="form.target_price"
                                type="number"
                                min="0"
                                step="0.01"
                        /></label>
                        <label
                            >Currency
                            <select v-model="form.target_currency">
                                <option value="">Select...</option>
                                <option>USD</option>
                                <option>EUR</option>
                                <option>AED</option>
                                <option>Other</option>
                            </select>
                        </label>
                        <label
                            >Delivery Country *<input
                                v-model="form.delivery_country"
                                type="text"
                                required
                        /></label>
                        <label
                            >Delivery City / Port<input
                                v-model="form.delivery_location"
                                type="text"
                        /></label>
                    </div>
                    <label
                        >Preferred Incoterm
                        <select v-model="form.incoterm">
                            <option value="">
                                Not sure — please recommend
                            </option>
                            <option>FOB</option>
                            <option>CIF</option>
                            <option>DAP</option>
                        </select>
                    </label>
                </section>

                <section
                    v-show="currentStep === 6"
                    data-step="6"
                    class="form-step"
                >
                    <h2>Timeline & Samples</h2>
                    <div class="field-grid">
                        <label
                            >Target Launch Date<input
                                v-model="form.launch_date"
                                type="date"
                        /></label>
                        <label
                            >Required Delivery Date<input
                                v-model="form.required_delivery_date"
                                type="date"
                        /></label>
                    </div>
                    <label
                        >Would you like samples? *
                        <select v-model="form.sample_request" required>
                            <option value="">Select...</option>
                            <option>Yes</option>
                            <option>After reviewing quotation</option>
                            <option>Not yet</option>
                        </select>
                    </label>
                    <fieldset>
                        <legend>Who will approve the project?</legend>
                        <div class="check-grid">
                            <label
                                v-for="[value, label] in decisionMakers"
                                :key="value"
                                class="check-option"
                            >
                                <input
                                    v-model="form.decision_makers"
                                    type="checkbox"
                                    :value="value"
                                />
                                {{ label }}
                            </label>
                        </div>
                    </fieldset>
                    <label
                        >Anything else we should know?
                        <textarea
                            v-model="form.comments"
                            rows="5"
                            placeholder="Launch deadlines, retailer requirements, commercial expectations, special requests..."
                        />
                    </label>
                </section>

                <section
                    v-show="currentStep === 7"
                    data-step="7"
                    class="form-step"
                >
                    <h2>Review Your Project</h2>
                    <p class="mb-5 text-sm text-[#625d52]">
                        Please check the information before submitting.
                    </p>
                    <div
                        v-for="group in reviewGroups"
                        :key="group.title"
                        class="review-group"
                    >
                        <h3 class="font-serif text-lg text-[#0f1c3f]">
                            {{ group.title }}
                        </h3>
                        <dl class="mt-2 divide-y divide-[#e7e1d4]">
                            <div
                                v-for="[label, value] in group.rows"
                                :key="label"
                                class="review-row"
                            >
                                <dt>{{ label }}</dt>
                                <dd>{{ display(value) }}</dd>
                            </div>
                        </dl>
                    </div>
                    <label class="check-option consent">
                        <input
                            v-model="form.accuracy_confirmation"
                            type="checkbox"
                            required
                        />
                        I confirm that the information provided is accurate to
                        the best of my knowledge.
                    </label>
                    <label class="check-option consent">
                        <input
                            v-model="form.privacy_consent"
                            type="checkbox"
                            required
                        />
                        I agree to Sevara using this information and uploaded
                        files to evaluate my project and contact me regarding
                        this enquiry.
                    </label>
                </section>

                <div
                    class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[#e7e1d4] pt-6"
                >
                    <button
                        v-if="currentStep > 0"
                        type="button"
                        class="secondary-btn"
                        @click="previousStep"
                    >
                        <ArrowLeft class="size-4" /> Back
                    </button>
                    <span v-else />
                    <button
                        v-if="currentStep < 7"
                        type="button"
                        class="primary-btn"
                        @click="nextStep"
                    >
                        Continue <ArrowRight class="size-4" />
                    </button>
                    <button
                        v-else
                        type="submit"
                        class="primary-btn"
                        :disabled="form.processing"
                    >
                        {{
                            form.processing
                                ? 'Submitting…'
                                : 'Submit Private Label Project'
                        }}
                    </button>
                </div>
                <p
                    v-if="form.progress"
                    class="mt-3 text-right text-xs text-[#625d52]"
                >
                    Uploading: {{ form.progress.percentage }}%
                </p>
            </form>
        </div>
    </section>
</template>

<style scoped>
.project-page {
    background: #f7f3e8;
}
.form-step h2 {
    margin-bottom: 1.5rem;
    font-family: Georgia, serif;
    font-size: 1.6rem;
    color: #0f1c3f;
}
.form-step h3 {
    margin-bottom: 0.5rem;
}
.project-form label:not(.check-option) {
    display: block;
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f1c3f;
}
.project-form input:not([type='checkbox']):not([type='file']),
.project-form select,
.project-form textarea {
    display: block;
    width: 100%;
    margin-top: 0.5rem;
    padding: 0.8rem 0.9rem;
    border: 1px solid #d6d0c5;
    border-radius: 0.375rem;
    background: #fff;
    color: #0f1c3f;
    font-weight: 400;
}
.project-form input:focus,
.project-form select:focus,
.project-form textarea:focus {
    outline: 2px solid #b99a45;
    outline-offset: 1px;
}
.project-form textarea {
    min-height: 7rem;
    resize: vertical;
}
.project-form fieldset {
    margin-bottom: 1.5rem;
}
.project-form legend {
    margin-bottom: 0.8rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f1c3f;
}
.field-grid,
.check-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.check-option {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #3b4353;
}
.check-option input {
    margin-top: 0.2rem;
    accent-color: #b99a45;
}
.sub-panel {
    margin: 1.5rem 0;
    padding: 1.5rem;
    border: 1px solid #e7e1d4;
    border-radius: 0.5rem;
    background: #fdfbf6;
}
.upload-box {
    margin: 1rem 0 1.5rem;
    padding: 1.2rem;
    border: 1px dashed #b9aa89;
    border-radius: 0.5rem;
    background: #fdfbf6;
}
.upload-box strong {
    display: block;
    color: #0f1c3f;
    font-size: 0.875rem;
}
.upload-box p,
.upload-box small {
    display: block;
    margin-top: 0.35rem;
    font-size: 0.75rem;
    line-height: 1.5;
    color: #625d52;
}
.upload-box input {
    display: block;
    max-width: 100%;
    margin-top: 0.8rem;
    font-size: 0.75rem;
}
.info-box {
    margin: 0.5rem 0 1.5rem;
    padding: 0.9rem 1rem;
    border-left: 3px solid #b99a45;
    background: #f7f3e8;
    color: #625d52;
    font-size: 0.875rem;
}
.sku-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 0.7rem;
    align-items: end;
    margin-bottom: 0.8rem;
}
.primary-btn,
.secondary-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border-radius: 0.25rem;
    padding: 0.75rem 1.2rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
}
.primary-btn {
    background: #0f1c3f;
    color: #fff;
}
.primary-btn:hover {
    background: #1b315b;
}
.secondary-btn {
    border: 1px solid #b99a45;
    background: #fff;
    color: #0f1c3f;
}
.secondary-btn:hover {
    background: #f7f3e8;
}
.primary-btn:disabled,
.secondary-btn:disabled {
    cursor: not-allowed;
    opacity: 0.55;
}
.remove-btn {
    margin-bottom: 1.25rem;
    padding: 0.7rem;
    color: #9a423e;
    cursor: pointer;
}
.field-error {
    margin-top: 0.5rem;
    color: #b42318;
    font-size: 0.8rem;
}
.review-group {
    margin-bottom: 1.4rem;
}
.review-row {
    display: grid;
    grid-template-columns: 11rem 1fr;
    gap: 0.75rem;
    padding: 0.65rem 0;
    font-size: 0.875rem;
}
.review-row dt {
    color: #625d52;
}
.review-row dd {
    overflow-wrap: anywhere;
    color: #0f1c3f;
}
.consent {
    margin-top: 1rem;
}
@media (max-width: 640px) {
    .field-grid,
    .check-grid,
    .sku-row {
        grid-template-columns: 1fr;
    }
    .remove-btn {
        justify-self: start;
        margin-bottom: 0;
    }
    .review-row {
        grid-template-columns: 1fr;
        gap: 0.2rem;
    }
}
</style>
