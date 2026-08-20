<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Package, Trash2 } from '@lucide/vue';
import PackagingFormatController from '@/actions/App/Http/Controllers/Admin/PackagingFormatController';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PackagingFormat } from '@/types';

const { format, returnTo } = defineProps<{
    format: PackagingFormat;
    index: number;
    total: number;
    returnTo: 'private-label' | 'settings';
}>();

const redirectOptions = { query: { return_to: returnTo } } as const;

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this packaging format?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <div
            class="flex items-center justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2.5"
        >
            <span class="truncate text-xs font-medium text-neutral-500">
                {{ index + 1 }}. {{ format.name }}
            </span>

            <div class="flex shrink-0 items-center gap-1">
                <Link
                    preserve-scroll
                    :href="
                        PackagingFormatController.moveUp(
                            format.id,
                            redirectOptions,
                        )
                    "
                    method="patch"
                    as="button"
                    :disabled="index === 0"
                    :class="
                        buttonVariants({ variant: 'ghost', size: 'icon-sm' })
                    "
                    aria-label="Move up"
                >
                    <ArrowUp />
                </Link>
                <Link
                    preserve-scroll
                    :href="
                        PackagingFormatController.moveDown(
                            format.id,
                            redirectOptions,
                        )
                    "
                    method="patch"
                    as="button"
                    :disabled="index === total - 1"
                    :class="
                        buttonVariants({ variant: 'ghost', size: 'icon-sm' })
                    "
                    aria-label="Move down"
                >
                    <ArrowDown />
                </Link>
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="
                        PackagingFormatController.destroy.form(
                            format.id,
                            redirectOptions,
                        )
                    "
                    v-slot="{ processing }"
                >
                    <Button
                        type="submit"
                        variant="ghost"
                        size="icon-sm"
                        :disabled="processing"
                        aria-label="Delete packaging format"
                        @click="confirmDelete"
                    >
                        <Trash2 />
                    </Button>
                </Form>
            </div>
        </div>

        <Form
            :options="{ preserveScroll: true }"
            v-bind="
                PackagingFormatController.update.form(
                    format.id,
                    redirectOptions,
                )
            "
            class="flex flex-col gap-4 p-4"
            v-slot="{ errors, processing, isDirty }"
        >
            <div class="flex items-start gap-4">
                <LazyImage
                    v-if="format.image_url"
                    :src="format.image_url"
                    :alt="format.name"
                    class="size-20 shrink-0 rounded-md border border-neutral-200 bg-white object-contain p-1"
                />
                <span
                    v-else
                    class="flex size-20 shrink-0 flex-col items-center justify-center gap-1 rounded-md border border-dashed border-neutral-300 text-neutral-400"
                >
                    <Package class="size-5" />
                    <span class="text-[10px]">No image</span>
                </span>

                <div class="grid flex-1 gap-2">
                    <Label :for="`format_${format.id}_image`">Image</Label>
                    <input
                        :id="`format_${format.id}_image`"
                        type="file"
                        name="image"
                        accept="image/*"
                        class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                    />
                    <p class="text-xs text-neutral-500">
                        Leave empty to keep the current image.
                    </p>
                    <InputError :message="errors.image" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label :for="`format_${format.id}_name`">Name</Label>
                    <Input
                        :id="`format_${format.id}_name`"
                        name="name"
                        class="h-11 text-base"
                        :default-value="format.name"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`format_${format.id}_description`">
                        Description
                    </Label>
                    <Input
                        :id="`format_${format.id}_description`"
                        name="description"
                        class="h-11 text-base"
                        :default-value="format.description ?? ''"
                        placeholder="Stand-up pouch for retail shelves and everyday sales."
                    />
                    <InputError :message="errors.description" />
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-sm text-neutral-900">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="size-4 rounded border-neutral-300"
                        :checked="format.is_featured"
                    />
                    Show in the main slider
                </label>

                <Button
                    type="submit"
                    variant="secondary"
                    :disabled="processing || !isDirty"
                >
                    Save changes
                </Button>
            </div>
        </Form>
    </div>
</template>
