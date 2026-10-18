<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Eye, Trash2 } from '@lucide/vue';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductExperienceItemController from '@/actions/App/Http/Controllers/Admin/ProductExperienceItemController';
import ProductImageController from '@/actions/App/Http/Controllers/Admin/ProductImageController';
import ProductSpecificationController from '@/actions/App/Http/Controllers/Admin/ProductSpecificationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { index as productsIndex } from '@/routes/admin/products';
import { show as viewProduct } from '@/routes/products';
import type { Product } from '@/types';

defineProps<{
    product: Product;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Products', href: productsIndex() },
            { title: 'Edit product', href: '' },
        ],
    },
});

function confirmDeleteProduct(event: MouseEvent): void {
    if (!confirm('Delete this product? This cannot be undone.')) {
        event.preventDefault();
    }
}

function confirmDeleteImage(event: MouseEvent): void {
    if (!confirm('Delete this image?')) {
        event.preventDefault();
    }
}

function confirmDeleteSpecification(event: MouseEvent): void {
    if (!confirm('Delete this specification?')) {
        event.preventDefault();
    }
}

function confirmDeleteExperienceItem(event: MouseEvent): void {
    if (!confirm('Delete this experience item?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head :title="`Edit ${product.title_part1} ${product.title_part2}`" />

    <div class="flex max-w-4xl flex-col space-y-10 px-6 py-8 lg:px-10">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Edit product"
                description="Update the product page content."
            />
            <a
                :href="viewProduct.url(product.slug)"
                target="_blank"
                rel="noopener"
                :class="buttonVariants({ variant: 'outline', size: 'sm' })"
            >
                <Eye />
                View live page
            </a>
        </div>

        <Separator />

        <div>
            <Heading
                variant="small"
                title="Page content"
                description="The title, colors and copy shown at the top of the product page."
            />

            <Form
                :options="{ preserveScroll: true }"
                v-bind="ProductController.update.form(product.id)"
                class="mt-6 flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-8"
                v-slot="{ errors, processing, isDirty }"
            >
                <div class="grid grid-cols-[1fr_auto] items-end gap-4">
                    <div class="grid gap-2">
                        <Label for="title_part1" class="text-neutral-900"
                            >Title &mdash; part 1 (larger)</Label
                        >
                        <Input
                            id="title_part1"
                            name="title_part1"
                            :default-value="product.title_part1"
                            class="h-11 text-base"
                            required
                        />
                        <InputError :message="errors.title_part1" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="title_part1_color" class="text-neutral-900"
                            >Color</Label
                        >
                        <input
                            id="title_part1_color"
                            type="color"
                            name="title_part1_color"
                            :value="product.title_part1_color"
                            class="h-11 w-16 cursor-pointer rounded-md border border-neutral-300 bg-white p-1"
                        />
                        <InputError :message="errors.title_part1_color" />
                    </div>
                </div>

                <div class="grid grid-cols-[1fr_auto] items-end gap-4">
                    <div class="grid gap-2">
                        <Label for="title_part2" class="text-neutral-900"
                            >Title &mdash; part 2 (smaller)</Label
                        >
                        <Input
                            id="title_part2"
                            name="title_part2"
                            :default-value="product.title_part2"
                            class="h-11 text-base"
                            required
                        />
                        <InputError :message="errors.title_part2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="title_part2_color" class="text-neutral-900"
                            >Color</Label
                        >
                        <input
                            id="title_part2_color"
                            type="color"
                            name="title_part2_color"
                            :value="product.title_part2_color"
                            class="h-11 w-16 cursor-pointer rounded-md border border-neutral-300 bg-white p-1"
                        />
                        <InputError :message="errors.title_part2_color" />
                    </div>
                </div>

                <p class="-mt-2 text-xs text-neutral-500">
                    Both parts together form the page title used for SEO.
                </p>

                <div class="grid gap-2">
                    <Label for="slug" class="text-neutral-900">URL slug</Label>
                    <Input
                        id="slug"
                        name="slug"
                        :default-value="product.slug"
                        class="h-11 text-base"
                        required
                    />
                    <p class="text-xs text-neutral-500">
                        Used in the page address, e.g. /products/{{
                            product.slug
                        }}
                    </p>
                    <InputError :message="errors.slug" />
                </div>

                <div class="grid grid-cols-[1fr_auto] items-end gap-4">
                    <div class="grid gap-2">
                        <Label for="subtitle" class="text-neutral-900"
                            >Subtitle</Label
                        >
                        <Input
                            id="subtitle"
                            name="subtitle"
                            :default-value="product.subtitle ?? ''"
                            class="h-11 text-base"
                            placeholder="Sweet Strawberry. Naturally Delightful."
                        />
                        <InputError :message="errors.subtitle" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="subtitle_color" class="text-neutral-900"
                            >Color</Label
                        >
                        <input
                            id="subtitle_color"
                            type="color"
                            name="subtitle_color"
                            :value="product.subtitle_color"
                            class="h-11 w-16 cursor-pointer rounded-md border border-neutral-300 bg-white p-1"
                        />
                        <InputError :message="errors.subtitle_color" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="description" class="text-neutral-900"
                        >Description</Label
                    >
                    <Textarea
                        id="description"
                        name="description"
                        :default-value="product.description ?? ''"
                        class="text-base"
                        placeholder="Premium date with almond, coated in smooth strawberry chocolate."
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-4">
                    <div>
                        <Label class="text-neutral-900"
                            >Technical product details</Label
                        >
                        <p class="mt-1 text-xs text-neutral-500">
                            These six details are shown above the Retail Ready
                            section on the product page.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label
                                for="net_piece_weight"
                                class="text-neutral-900"
                                >Net piece weight</Label
                            >
                            <Input
                                id="net_piece_weight"
                                name="net_piece_weight"
                                :default-value="product.net_piece_weight ?? ''"
                                placeholder="25 g"
                            />
                            <InputError :message="errors.net_piece_weight" />
                        </div>

                        <div class="grid gap-2">
                            <Label
                                for="pieces_per_pack"
                                class="text-neutral-900"
                                >Pieces per pack</Label
                            >
                            <Input
                                id="pieces_per_pack"
                                name="pieces_per_pack"
                                :default-value="product.pieces_per_pack ?? ''"
                                placeholder="12 pieces"
                            />
                            <InputError :message="errors.pieces_per_pack" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="shelf_life" class="text-neutral-900"
                                >Shelf life</Label
                            >
                            <Input
                                id="shelf_life"
                                name="shelf_life"
                                :default-value="product.shelf_life ?? ''"
                                placeholder="12 months"
                            />
                            <InputError :message="errors.shelf_life" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="storage" class="text-neutral-900"
                                >Storage</Label
                            >
                            <Input
                                id="storage"
                                name="storage"
                                :default-value="product.storage ?? ''"
                                placeholder="Cool, dry place"
                            />
                            <InputError :message="errors.storage" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="allergens" class="text-neutral-900"
                                >Allergens</Label
                            >
                            <Input
                                id="allergens"
                                name="allergens"
                                :default-value="product.allergens ?? ''"
                                placeholder="Contains almonds"
                            />
                            <InputError :message="errors.allergens" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="moq" class="text-neutral-900"
                                >MOQ</Label
                            >
                            <Input
                                id="moq"
                                name="moq"
                                :default-value="product.moq ?? ''"
                                placeholder="500 cartons"
                            />
                            <InputError :message="errors.moq" />
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label
                            for="technical_data_sheet"
                            class="text-neutral-900"
                            >Technical data sheet</Label
                        >
                        <a
                            v-if="product.technical_data_sheet_url"
                            :href="product.technical_data_sheet_url"
                            target="_blank"
                            rel="noopener"
                            class="text-xs font-medium text-[#a8842b] hover:underline"
                        >
                            View current file
                        </a>
                        <input
                            id="technical_data_sheet"
                            type="file"
                            name="technical_data_sheet"
                            accept=".pdf,image/*"
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                        <InputError :message="errors.technical_data_sheet" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="pieces_per_pack_file"
                            class="text-neutral-900"
                            >Pieces-per-pack file</Label
                        >
                        <a
                            v-if="product.pieces_per_pack_url"
                            :href="product.pieces_per_pack_url"
                            target="_blank"
                            rel="noopener"
                            class="text-xs font-medium text-[#a8842b] hover:underline"
                        >
                            View current file
                        </a>
                        <input
                            id="pieces_per_pack_file"
                            type="file"
                            name="pieces_per_pack_file"
                            accept=".pdf,image/*"
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                        <InputError :message="errors.pieces_per_pack_file" />
                    </div>
                </div>

                <Button
                    type="submit"
                    size="lg"
                    class="w-fit"
                    :disabled="processing || !isDirty"
                >
                    Save changes
                </Button>
            </Form>
        </div>

        <Separator />

        <div>
            <Heading
                variant="small"
                title="Gallery"
                description="Add or remove the images shown on this product's page."
            />

            <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-8">
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="ProductController.update.form(product.id)"
                    class="mb-8 grid gap-4 border-b border-neutral-200 pb-8"
                    v-slot="{ errors, processing }"
                >
                    <input type="hidden" name="slug" :value="product.slug" />
                    <input
                        type="hidden"
                        name="title_part1"
                        :value="product.title_part1"
                    />
                    <input
                        type="hidden"
                        name="title_part1_color"
                        :value="product.title_part1_color"
                    />
                    <input
                        type="hidden"
                        name="title_part2"
                        :value="product.title_part2"
                    />
                    <input
                        type="hidden"
                        name="title_part2_color"
                        :value="product.title_part2_color"
                    />
                    <input
                        type="hidden"
                        name="subtitle_color"
                        :value="product.subtitle_color"
                    />

                    <div class="grid gap-2">
                        <Label for="spec_image" class="text-neutral-900"
                            >Main image for this section</Label
                        >
                        <div class="flex items-center gap-3">
                            <LazyImage
                                v-if="product.spec_image_url"
                                :src="product.spec_image_url"
                                alt=""
                                class="h-16 w-24 rounded-md border border-neutral-200 object-cover"
                            />
                            <input
                                id="spec_image"
                                type="file"
                                name="spec_image"
                                accept="image/*"
                                class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                            />
                        </div>
                        <InputError :message="errors.spec_image" />
                    </div>

                    <Button
                        type="submit"
                        variant="secondary"
                        class="w-fit"
                        :disabled="processing"
                    >
                        Save main image
                    </Button>
                </Form>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="ProductImageController.store.form(product.id)"
                    reset-on-success
                    class="flex flex-wrap items-end gap-3"
                    v-slot="{ errors, processing, isDirty }"
                >
                    <div class="grid min-w-64 flex-1 gap-2">
                        <Label for="image" class="text-neutral-900"
                            >Add image</Label
                        >
                        <input
                            id="image"
                            type="file"
                            name="image"
                            accept="image/*"
                            required
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                        <InputError :message="errors.image" />
                    </div>
                    <Button
                        type="submit"
                        variant="secondary"
                        :disabled="processing || !isDirty"
                    >
                        Upload
                    </Button>
                </Form>

                <div
                    v-if="product.images.length === 0"
                    class="mt-6 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                >
                    No images yet. Upload one above.
                </div>

                <div v-else class="mt-6 grid gap-5 sm:grid-cols-3">
                    <div
                        v-for="(image, index) in product.images"
                        :key="image.id"
                        class="overflow-hidden rounded-xl border border-neutral-200"
                    >
                        <div
                            class="flex aspect-square items-center justify-center bg-white p-4"
                        >
                            <LazyImage
                                :src="image.image_url"
                                alt=""
                                class="max-h-full max-w-full object-contain"
                            />
                        </div>
                        <div
                            class="flex items-center justify-center gap-1 border-t border-neutral-200 p-2"
                        >
                            <Link
                                preserve-scroll
                                :href="
                                    ProductImageController.moveUp({
                                        product: product.id,
                                        image: image.id,
                                    })
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
                                    ProductImageController.moveDown({
                                        product: product.id,
                                        image: image.id,
                                    })
                                "
                                method="patch"
                                as="button"
                                :disabled="index === product.images.length - 1"
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
                                    ProductImageController.destroy.form({
                                        product: product.id,
                                        image: image.id,
                                    })
                                "
                                v-slot="{ processing }"
                            >
                                <Button
                                    type="submit"
                                    variant="ghost"
                                    size="icon-sm"
                                    :disabled="processing"
                                    aria-label="Delete image"
                                    @click="confirmDeleteImage"
                                >
                                    <Trash2 />
                                </Button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Separator />

        <div>
            <Heading
                variant="small"
                title="Product highlights"
                description="Icon and text details shown directly below the product buttons."
            />

            <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-8">
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="
                        ProductExperienceItemController.store.form(product.id)
                    "
                    reset-on-success
                    class="grid gap-4"
                    v-slot="{ errors, processing, isDirty }"
                >
                    <div class="grid gap-2">
                        <Label for="experience_icon" class="text-neutral-900"
                            >Icon</Label
                        >
                        <input
                            id="experience_icon"
                            type="file"
                            name="icon"
                            accept="image/*"
                            required
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                        <InputError :message="errors.icon" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="experience_item_title"
                            class="text-neutral-900"
                            >Highlight text</Label
                        >
                        <Input
                            id="experience_item_title"
                            name="title"
                            class="h-11 text-base"
                            placeholder="Whole Almond Filling"
                            required
                        />
                        <InputError :message="errors.title" />
                    </div>

                    <Button
                        type="submit"
                        variant="secondary"
                        class="w-fit"
                        :disabled="processing || !isDirty"
                    >
                        Add product highlight
                    </Button>
                </Form>

                <div
                    v-if="product.experience_items.length === 0"
                    class="mt-6 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                >
                    No experience items yet. Add one above.
                </div>

                <div
                    v-else
                    class="mt-6 flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl border border-neutral-200"
                >
                    <div
                        v-for="(item, index) in product.experience_items"
                        :key="item.id"
                        class="flex items-center gap-3 p-3"
                    >
                        <LazyImage
                            :src="item.icon_url"
                            alt=""
                            class="size-10 shrink-0 rounded-md border border-neutral-200 bg-white object-contain p-1"
                        />
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-neutral-900"
                            >
                                {{ item.title }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <Link
                                preserve-scroll
                                :href="
                                    ProductExperienceItemController.moveUp({
                                        product: product.id,
                                        experienceItem: item.id,
                                    })
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
                                    ProductExperienceItemController.moveDown({
                                        product: product.id,
                                        experienceItem: item.id,
                                    })
                                "
                                method="patch"
                                as="button"
                                :disabled="
                                    index ===
                                    product.experience_items.length - 1
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
                                    ProductExperienceItemController.destroy.form(
                                        {
                                            product: product.id,
                                            experienceItem: item.id,
                                        },
                                    )
                                "
                                v-slot="{ processing }"
                            >
                                <Button
                                    type="submit"
                                    variant="ghost"
                                    size="icon-sm"
                                    :disabled="processing"
                                    aria-label="Delete experience item"
                                    @click="confirmDeleteExperienceItem"
                                >
                                    <Trash2 />
                                </Button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Separator />

        <div>
            <Heading
                variant="small"
                title="Product specifications"
                description="Product details shown on the product page. Each has an icon, a title and a value."
            />

            <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-8">
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="
                        ProductSpecificationController.store.form(product.id)
                    "
                    reset-on-success
                    class="grid gap-4 sm:grid-cols-2"
                    v-slot="{ errors, processing, isDirty }"
                >
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="spec_icon" class="text-neutral-900"
                            >Icon</Label
                        >
                        <input
                            id="spec_icon"
                            type="file"
                            name="icon"
                            accept="image/*"
                            required
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                        <InputError :message="errors.icon" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="spec_question" class="text-neutral-900"
                            >Title</Label
                        >
                        <Input
                            id="spec_question"
                            name="question"
                            class="h-11 text-base"
                            placeholder="Ingredients"
                            required
                        />
                        <InputError :message="errors.question" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="spec_answer" class="text-neutral-900"
                            >Value</Label
                        >
                        <Input
                            id="spec_answer"
                            name="answer"
                            class="h-11 text-base"
                            placeholder="Dates, almonds, strawberry chocolate."
                            required
                        />
                        <InputError :message="errors.answer" />
                    </div>

                    <Button
                        type="submit"
                        variant="secondary"
                        class="w-fit"
                        :disabled="processing || !isDirty"
                    >
                        Add specification
                    </Button>
                </Form>

                <div
                    v-if="product.specifications.length === 0"
                    class="mt-6 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
                >
                    No specifications yet. Add one above.
                </div>

                <div
                    v-else
                    class="mt-6 flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl border border-neutral-200"
                >
                    <div
                        v-for="(spec, index) in product.specifications"
                        :key="spec.id"
                        class="flex items-center gap-3 p-3"
                    >
                        <LazyImage
                            :src="spec.icon_url"
                            alt=""
                            class="size-10 shrink-0 rounded-md border border-neutral-200 bg-white object-contain p-1"
                        />
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-neutral-900"
                            >
                                {{ spec.question }}
                            </p>
                            <p class="truncate text-sm text-neutral-500">
                                {{ spec.answer }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <Link
                                preserve-scroll
                                :href="
                                    ProductSpecificationController.moveUp({
                                        product: product.id,
                                        specification: spec.id,
                                    })
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
                                    ProductSpecificationController.moveDown({
                                        product: product.id,
                                        specification: spec.id,
                                    })
                                "
                                method="patch"
                                as="button"
                                :disabled="
                                    index === product.specifications.length - 1
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
                                    ProductSpecificationController.destroy.form(
                                        {
                                            product: product.id,
                                            specification: spec.id,
                                        },
                                    )
                                "
                                v-slot="{ processing }"
                            >
                                <Button
                                    type="submit"
                                    variant="ghost"
                                    size="icon-sm"
                                    :disabled="processing"
                                    aria-label="Delete specification"
                                    @click="confirmDeleteSpecification"
                                >
                                    <Trash2 />
                                </Button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Separator />

        <div
            class="flex items-center justify-between gap-4 rounded-xl border border-red-200 bg-white p-6"
        >
            <div>
                <p class="text-sm font-medium text-neutral-900">
                    Delete this product
                </p>
                <p class="text-sm text-neutral-500">
                    This removes the page and all its images permanently.
                </p>
            </div>
            <Form
                v-bind="ProductController.destroy.form(product.id)"
                v-slot="{ processing }"
            >
                <Button
                    type="submit"
                    variant="outline"
                    class="shrink-0 border-red-300 text-red-600 hover:bg-red-50 hover:text-red-700"
                    :disabled="processing"
                    @click="confirmDeleteProduct"
                >
                    Delete product
                </Button>
            </Form>
        </div>
    </div>
</template>
