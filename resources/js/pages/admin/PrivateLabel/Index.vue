<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Eye, Inbox, Trash2 } from '@lucide/vue';
import PrivateLabelItemController from '@/actions/App/Http/Controllers/Admin/PrivateLabelItemController';
import PackagingFormatManager from '@/components/admin/PackagingFormatManager.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { privateLabel } from '@/routes';
import { index as privateLabelIndex } from '@/routes/admin/private-label';
import { index as inquiriesIndex } from '@/routes/admin/private-label/inquiries';
import type { PackagingFormat, PrivateLabelAdminSection } from '@/types';

defineProps<{
    sections: PrivateLabelAdminSection[];
    packagingFormats: PackagingFormat[];
    unreadInquiries: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Private Label', href: privateLabelIndex() }],
    },
});

function confirmDeleteItem(event: MouseEvent): void {
    if (!confirm('Delete this item?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Private Label" />

    <div class="flex max-w-4xl flex-col space-y-10 bg-white px-6 py-8 lg:px-10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Private Label"
                description="Manage the content shown on the public Private Label page."
            />
            <div class="flex items-center gap-2">
                <Link
                    :href="inquiriesIndex()"
                    :class="buttonVariants({ variant: 'outline', size: 'sm' })"
                >
                    <Inbox />
                    Inquiries
                    <span
                        v-if="unreadInquiries > 0"
                        class="ml-1 rounded-full bg-neutral-900 px-2 py-0.5 text-xs text-white"
                    >
                        {{ unreadInquiries }}
                    </span>
                </Link>
                <a
                    :href="privateLabel.url()"
                    target="_blank"
                    rel="noopener"
                    :class="buttonVariants({ variant: 'outline', size: 'sm' })"
                >
                    <Eye />
                    View live page
                </a>
            </div>
        </div>

        <template v-for="section in sections" :key="section.value">
            <section
                class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 lg:p-7"
            >
                <Heading
                    :title="section.label"
                    :description="section.description"
                />

                <div
                    class="rounded-xl border border-neutral-200 bg-white p-6 lg:p-8"
                >
                    <Form
                        :options="{ preserveScroll: true }"
                        v-bind="PrivateLabelItemController.store.form()"
                        reset-on-success
                        class="grid gap-4 sm:grid-cols-2"
                        v-slot="{ errors, processing, isDirty }"
                    >
                        <input
                            type="hidden"
                            name="section"
                            :value="section.value"
                        />

                        <div class="grid gap-2">
                            <Label
                                :for="`${section.value}_title`"
                                class="text-neutral-900"
                            >
                                Title
                            </Label>
                            <Input
                                :id="`${section.value}_title`"
                                name="title"
                                class="h-11 text-base"
                                placeholder="Product Development"
                                required
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="grid gap-2">
                            <Label
                                :for="`${section.value}_description`"
                                class="text-neutral-900"
                            >
                                Description
                            </Label>
                            <Input
                                :id="`${section.value}_description`"
                                name="description"
                                class="h-11 text-base"
                                placeholder="Custom flavors and recipes developed for your market."
                                required
                            />
                            <InputError :message="errors.description" />
                        </div>

                        <div class="grid gap-2 sm:col-span-2">
                            <Label
                                :for="`${section.value}_icon`"
                                class="text-neutral-900"
                            >
                                Icon (optional)
                            </Label>
                            <input
                                :id="`${section.value}_icon`"
                                type="file"
                                name="icon"
                                accept="image/*"
                                class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                            />
                            <p class="text-xs text-neutral-500">
                                Leave empty to use the built-in gold line icon.
                            </p>
                            <InputError :message="errors.icon" />
                        </div>

                        <Button
                            type="submit"
                            variant="secondary"
                            class="w-fit"
                            :disabled="processing || !isDirty"
                        >
                            Add item
                        </Button>
                    </Form>

                    <div
                        v-if="section.items.length === 0"
                        class="mt-6 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                    >
                        Nothing here yet. Add an item above.
                    </div>

                    <div
                        v-else
                        class="mt-6 flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl border border-neutral-200"
                    >
                        <div
                            v-for="(item, index) in section.items"
                            :key="item.id"
                            class="flex items-center gap-3 p-3"
                        >
                            <LazyImage
                                v-if="item.icon_url"
                                :src="item.icon_url"
                                alt=""
                                class="size-10 shrink-0 rounded-md border border-neutral-200 bg-white object-contain p-1"
                            />
                            <span
                                v-else
                                class="flex size-10 shrink-0 items-center justify-center rounded-md border border-dashed border-neutral-300 text-xs text-neutral-400"
                            >
                                {{ index + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-neutral-900"
                                >
                                    {{ item.title }}
                                </p>
                                <p class="truncate text-sm text-neutral-500">
                                    {{ item.description }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-1">
                                <Link
                                    preserve-scroll
                                    :href="
                                        PrivateLabelItemController.moveUp(
                                            item.id,
                                        )
                                    "
                                    method="patch"
                                    as="button"
                                    :disabled="index === 0"
                                    :class="
                                        buttonVariants({
                                            variant: 'ghost',
                                            size: 'icon-sm',
                                        })
                                    "
                                    aria-label="Move up"
                                >
                                    <ArrowUp />
                                </Link>
                                <Link
                                    preserve-scroll
                                    :href="
                                        PrivateLabelItemController.moveDown(
                                            item.id,
                                        )
                                    "
                                    method="patch"
                                    as="button"
                                    :disabled="
                                        index === section.items.length - 1
                                    "
                                    :class="
                                        buttonVariants({
                                            variant: 'ghost',
                                            size: 'icon-sm',
                                        })
                                    "
                                    aria-label="Move down"
                                >
                                    <ArrowDown />
                                </Link>
                                <Form
                                    :options="{ preserveScroll: true }"
                                    v-bind="
                                        PrivateLabelItemController.destroy.form(
                                            item.id,
                                        )
                                    "
                                    v-slot="{ processing }"
                                >
                                    <Button
                                        type="submit"
                                        variant="ghost"
                                        size="icon-sm"
                                        :disabled="processing"
                                        aria-label="Delete item"
                                        @click="confirmDeleteItem"
                                    >
                                        <Trash2 />
                                    </Button>
                                </Form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <PackagingFormatManager
            :packaging-formats="packagingFormats"
            return-to="private-label"
        />
    </div>
</template>
