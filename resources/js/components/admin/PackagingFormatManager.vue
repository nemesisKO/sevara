<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import PackagingFormatController from '@/actions/App/Http/Controllers/Admin/PackagingFormatController';
import PackagingFormatEditor from '@/components/admin/PackagingFormatEditor.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PackagingFormat } from '@/types';

const { packagingFormats, returnTo } = defineProps<{
    packagingFormats: PackagingFormat[];
    returnTo: 'private-label' | 'settings';
}>();

const featuredFormats = computed(() =>
    packagingFormats.filter((format) => format.is_featured),
);

const otherFormats = computed(() =>
    packagingFormats.filter((format) => !format.is_featured),
);

const redirectOptions = { query: { return_to: returnTo } } as const;
</script>

<template>
    <section
        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 lg:p-7"
    >
        <Heading
            title="Product format options"
            description="These options appear in the product format field on both the Contact and Private Label forms. The current options are kept as the defaults."
        />

        <div class="rounded-xl border border-neutral-200 bg-white p-6 lg:p-8">
            <Form
                :options="{ preserveScroll: true }"
                v-bind="PackagingFormatController.store.form(redirectOptions)"
                reset-on-success
                class="grid gap-4 sm:grid-cols-2"
                v-slot="{ errors, processing, isDirty }"
            >
                <div class="grid gap-2">
                    <Label for="format_name" class="text-neutral-900">
                        Name
                    </Label>
                    <Input
                        id="format_name"
                        name="name"
                        class="h-11 text-base"
                        placeholder="250g Retail Pouch"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="format_description" class="text-neutral-900">
                        Description
                    </Label>
                    <Input
                        id="format_description"
                        name="description"
                        class="h-11 text-base"
                        placeholder="Stand-up pouch for retail shelves and everyday sales."
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label for="format_image" class="text-neutral-900">
                        Image (optional)
                    </Label>
                    <input
                        id="format_image"
                        type="file"
                        name="image"
                        accept="image/*"
                        class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                    />
                    <InputError :message="errors.image" />
                </div>

                <label
                    class="flex items-center gap-2 text-sm text-neutral-900 sm:col-span-2"
                >
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="size-4 rounded border-neutral-300"
                        checked
                    />
                    Show in the main slider
                </label>

                <Button
                    type="submit"
                    variant="secondary"
                    class="w-fit"
                    :disabled="processing || !isDirty"
                >
                    Add product format
                </Button>
            </Form>

            <div class="mt-10 border-t border-neutral-200 pt-8">
                <p class="text-base font-semibold text-neutral-900">
                    Slider formats
                </p>
                <p class="mt-1 text-sm text-neutral-500">
                    Shown in the main packaging-format area on the Private Label
                    page.
                </p>

                <div
                    v-if="featuredFormats.length === 0"
                    class="mt-4 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                >
                    No slider formats yet. Add one above.
                </div>

                <div v-else class="mt-4 flex flex-col gap-4">
                    <PackagingFormatEditor
                        v-for="(format, index) in featuredFormats"
                        :key="format.id"
                        :format="format"
                        :index="index"
                        :total="featuredFormats.length"
                        :return-to="returnTo"
                    />
                </div>
            </div>

            <div class="mt-10 border-t border-neutral-200 pt-8">
                <p class="text-base font-semibold text-neutral-900">
                    Other available formats
                </p>
                <p class="mt-1 text-sm text-neutral-500">
                    Available in the form dropdown and shown in the smaller
                    format grid.
                </p>

                <div
                    v-if="otherFormats.length === 0"
                    class="mt-4 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                >
                    No other formats yet. Add one above and leave "Show in the
                    main slider" unchecked.
                </div>

                <div v-else class="mt-4 flex flex-col gap-4">
                    <PackagingFormatEditor
                        v-for="(format, index) in otherFormats"
                        :key="format.id"
                        :format="format"
                        :index="index"
                        :total="otherFormats.length"
                        :return-to="returnTo"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
