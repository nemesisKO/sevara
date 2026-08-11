<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as productsIndex } from '@/routes/admin/products';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Products', href: productsIndex() },
            { title: 'Add product', href: '' },
        ],
    },
});
</script>

<template>
    <Head title="Add Product" />

    <div class="flex max-w-2xl flex-col space-y-8">
        <Heading
            variant="small"
            title="Add product"
            description="Create the product page. You can manage its image gallery after saving."
        />

        <Form
            v-bind="ProductController.store.form()"
            class="flex flex-col gap-6 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="title_part1"
                        >Title &mdash; part 1 (larger)</Label
                    >
                    <Input
                        id="title_part1"
                        name="title_part1"
                        placeholder="Strawberry"
                        required
                    />
                    <InputError :message="errors.title_part1" />
                </div>
                <div class="grid gap-2">
                    <Label for="title_part1_color">Part 1 color</Label>
                    <input
                        id="title_part1_color"
                        type="color"
                        name="title_part1_color"
                        value="#a83b52"
                        class="h-9 w-16 cursor-pointer rounded-md border border-input bg-transparent p-1"
                    />
                    <InputError :message="errors.title_part1_color" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="title_part2"
                        >Title &mdash; part 2 (smaller)</Label
                    >
                    <Input
                        id="title_part2"
                        name="title_part2"
                        placeholder="Chocolate Date"
                        required
                    />
                    <InputError :message="errors.title_part2" />
                </div>
                <div class="grid gap-2">
                    <Label for="title_part2_color">Part 2 color</Label>
                    <input
                        id="title_part2_color"
                        type="color"
                        name="title_part2_color"
                        value="#0f1c3f"
                        class="h-9 w-16 cursor-pointer rounded-md border border-input bg-transparent p-1"
                    />
                    <InputError :message="errors.title_part2_color" />
                </div>
            </div>

            <p class="-mt-2 text-xs text-muted-foreground">
                Both parts together form the page title used for SEO.
            </p>

            <div class="grid gap-2">
                <Label for="slug">URL slug</Label>
                <Input
                    id="slug"
                    name="slug"
                    placeholder="strawberry-chocolate-date"
                    required
                />
                <p class="text-xs text-muted-foreground">
                    Used in the page address, e.g.
                    /products/strawberry-chocolate-date
                </p>
                <InputError :message="errors.slug" />
            </div>

            <div class="grid gap-4 sm:grid-cols-[1fr_auto]">
                <div class="grid gap-2">
                    <Label for="subtitle">Subtitle</Label>
                    <Input
                        id="subtitle"
                        name="subtitle"
                        placeholder="Sweet Strawberry. Naturally Delightful."
                    />
                    <InputError :message="errors.subtitle" />
                </div>
                <div class="grid gap-2">
                    <Label for="subtitle_color">Subtitle color</Label>
                    <input
                        id="subtitle_color"
                        type="color"
                        name="subtitle_color"
                        value="#a83b52"
                        class="h-9 w-16 cursor-pointer rounded-md border border-input bg-transparent p-1"
                    />
                    <InputError :message="errors.subtitle_color" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    placeholder="Premium date with almond, coated in smooth strawberry chocolate."
                />
                <InputError :message="errors.description" />
            </div>

            <Button
                type="submit"
                size="lg"
                class="w-fit"
                :disabled="processing"
            >
                Create product
            </Button>
        </Form>
    </div>
</template>
