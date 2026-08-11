<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import Heading from '@/components/Heading.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    create as createProduct,
    index as productsIndex,
} from '@/routes/admin/products';
import type { Product } from '@/types';

defineProps<{
    products: Product[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Products', href: productsIndex() }],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this product? This cannot be undone.')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Products" />

    <div class="flex flex-col space-y-8 p-8">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Products"
                description="Manage the product pages shown on the website"
            />
            <Link
                :href="createProduct()"
                :class="buttonVariants({ size: 'sm' })"
            >
                <Plus />
                Add product
            </Link>
        </div>

        <div
            v-if="products.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No products yet. Add one to create your first product page.
        </div>

        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="product in products"
                :key="product.id"
                class="overflow-hidden rounded-xl border bg-card"
            >
                <div
                    class="flex aspect-video items-center justify-center bg-white p-6"
                >
                    <LazyImage
                        v-if="product.images[0]"
                        :src="product.images[0].image_url"
                        alt=""
                        class="max-h-full max-w-full object-contain"
                    />
                    <span v-else class="text-xs text-muted-foreground"
                        >No image</span
                    >
                </div>
                <div class="flex items-center justify-between gap-2 p-4">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">
                            {{ product.title_part1 }} {{ product.title_part2 }}
                        </p>
                        <p class="truncate text-sm text-muted-foreground">
                            /products/{{ product.slug }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            :href="ProductController.edit(product.id)"
                            :class="
                                buttonVariants({ variant: 'ghost', size: 'sm' })
                            "
                        >
                            Edit
                        </Link>
                        <Form
                            :options="{ preserveScroll: true }"
                            v-bind="ProductController.destroy.form(product.id)"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="processing"
                                aria-label="Delete product"
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
