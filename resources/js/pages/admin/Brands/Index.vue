<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Trash2 } from '@lucide/vue';
import BrandController from '@/actions/App/Http/Controllers/Admin/BrandController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as brandsIndex } from '@/routes/admin/brands';
import type { Brand } from '@/types';

defineProps<{
    brands: Brand[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Brands',
                href: brandsIndex(),
            },
        ],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this brand?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Brands" />

    <div class="flex flex-col space-y-8">
        <Heading
            variant="small"
            title="Brands"
            description="Manage the brands shown in the homepage brand network section"
        />

        <Form
            :options="{ preserveScroll: true }"
            v-bind="BrandController.store.form()"
            reset-on-success
            class="flex max-w-md flex-col gap-5 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    placeholder="Brand name"
                    required
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="image">Logo</Label>
                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/*"
                    required
                    class="h-10 w-full rounded-md border border-input bg-transparent text-sm file:mr-3 file:h-10 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground"
                />
                <InputError :message="errors.image" />
            </div>

            <div class="grid gap-2">
                <Label for="link">Link</Label>
                <Input
                    id="link"
                    name="link"
                    placeholder="https://..."
                    required
                />
                <InputError :message="errors.link" />
            </div>

            <Button type="submit" size="lg" :disabled="processing">
                Add brand
            </Button>
        </Form>

        <div
            v-if="brands.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No brands yet. Add one above to populate the brand network section.
        </div>

        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="(brand, index) in brands"
                :key="brand.id"
                class="overflow-hidden rounded-xl border bg-card"
            >
                <div
                    class="flex aspect-video items-center justify-center bg-white p-6"
                >
                    <LazyImage
                        :src="brand.image_url"
                        :alt="brand.name"
                        class="max-h-full max-w-full object-contain"
                    />
                </div>
                <div class="flex items-center justify-between gap-2 p-4">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">
                            {{ brand.name }}
                        </p>
                        <a
                            :href="brand.link"
                            target="_blank"
                            rel="noopener"
                            class="truncate text-sm text-muted-foreground hover:text-foreground"
                        >
                            {{ brand.link }}
                        </a>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            preserve-scroll
                            :href="BrandController.moveUp(brand.id)"
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
                            :href="BrandController.moveDown(brand.id)"
                            method="patch"
                            as="button"
                            :disabled="index === brands.length - 1"
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
                            v-bind="BrandController.destroy.form(brand.id)"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="processing"
                                aria-label="Delete brand"
                                @click="confirmDelete"
                            >
                                <Trash2 />
                            </Button>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
