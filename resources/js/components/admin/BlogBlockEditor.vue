<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import BlogBlockController from '@/actions/App/Http/Controllers/Admin/BlogBlockController';
import RichTextEditor from '@/components/admin/RichTextEditor.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Blog, BlogBlock, BlogBlockTableCell } from '@/types';

const props = defineProps<{
    blog: Blog;
    block: BlogBlock;
    index: number;
    total: number;
}>();

const typeLabels: Record<string, string> = {
    article: 'Text',
    image: 'Image',
    hint: 'Hint',
    table: 'Table',
};

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this block?')) {
        event.preventDefault();
    }
}

function emptyCell(): BlogBlockTableCell {
    return { text: '', link_url: '' };
}

const rows = ref<BlogBlockTableCell[][]>(
    props.block.table_data && props.block.table_data.length > 0
        ? props.block.table_data.map((row) => row.map((cell) => ({ ...cell })))
        : [[emptyCell()]],
);

const columnCount = computed(() => rows.value[0]?.length ?? 0);

function addTableRow(): void {
    rows.value.push(
        Array.from({ length: columnCount.value || 1 }, () => emptyCell()),
    );
}

function addTableColumn(): void {
    rows.value.forEach((row) => row.push(emptyCell()));
}

function removeTableRow(): void {
    if (rows.value.length > 1) {
        rows.value.pop();
    }
}

function removeTableColumn(): void {
    if (columnCount.value > 1) {
        rows.value.forEach((row) => row.pop());
    }
}
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <div
            class="flex items-center justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2.5"
        >
            <span class="text-xs font-medium text-neutral-500">
                Block {{ index + 1 }} &middot; {{ typeLabels[block.type] }}
            </span>

            <div class="flex shrink-0 items-center gap-1">
                <Link
                    preserve-scroll
                    :href="
                        BlogBlockController.moveUp({
                            blog: blog.id,
                            block: block.id,
                        })
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
                        BlogBlockController.moveDown({
                            blog: blog.id,
                            block: block.id,
                        })
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
                        BlogBlockController.destroy.form({
                            blog: blog.id,
                            block: block.id,
                        })
                    "
                    v-slot="{ processing }"
                >
                    <Button
                        type="submit"
                        variant="ghost"
                        size="icon-sm"
                        :disabled="processing"
                        aria-label="Delete block"
                        @click="confirmDelete"
                    >
                        <Trash2 />
                    </Button>
                </Form>
            </div>
        </div>

        <div class="p-4">
            <Form
                :options="{ preserveScroll: true }"
                v-bind="
                    BlogBlockController.update.form({
                        blog: blog.id,
                        block: block.id,
                    })
                "
                class="flex flex-col gap-4"
                v-slot="{ errors, processing, isDirty }"
            >
                <template v-if="block.type === 'article'">
                    <RichTextEditor
                        name="body"
                        :default-value="block.body ?? ''"
                        placeholder="Write the paragraph content..."
                    />
                    <InputError :message="errors.body" />
                </template>

                <template v-else-if="block.type === 'image'">
                    <div class="flex items-center gap-4">
                        <LazyImage
                            v-if="block.image_url"
                            :src="block.image_url"
                            alt=""
                            class="h-20 w-32 shrink-0 rounded-md border border-neutral-200 object-cover"
                        />
                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                    </div>
                    <InputError :message="errors.image" />
                </template>

                <template v-else-if="block.type === 'hint'">
                    <div class="grid gap-3">
                        <div class="grid gap-2">
                            <Label class="text-neutral-900">Title</Label>
                            <Input
                                name="title"
                                :default-value="block.title ?? ''"
                                placeholder="Did you know?"
                            />
                            <InputError :message="errors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label class="text-neutral-900">Text</Label>
                            <Textarea
                                name="body"
                                :default-value="block.body ?? ''"
                                placeholder="Short supporting note..."
                            />
                            <InputError :message="errors.body" />
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label class="text-neutral-900"
                                    >Link text</Label
                                >
                                <Input
                                    name="link_text"
                                    :default-value="block.link_text ?? ''"
                                    placeholder="Read more"
                                />
                                <InputError :message="errors.link_text" />
                            </div>
                            <div class="grid gap-2">
                                <Label class="text-neutral-900">Link URL</Label>
                                <Input
                                    name="link_url"
                                    :default-value="block.link_url ?? ''"
                                    placeholder="/blog/example-post"
                                />
                                <InputError :message="errors.link_url" />
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else-if="block.type === 'table'">
                    <div class="grid gap-3">
                        <div class="grid gap-2">
                            <Label class="text-neutral-900">Title</Label>
                            <Input
                                name="title"
                                :default-value="block.title ?? ''"
                                placeholder="Comparison table"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs text-neutral-700 hover:bg-white"
                                @click="addTableRow"
                            >
                                <Plus class="size-3.5" /> Row
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs text-neutral-700 hover:bg-white"
                                @click="addTableColumn"
                            >
                                <Plus class="size-3.5" /> Column
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md border border-red-100 bg-red-50 px-3 py-1.5 text-xs text-red-700 hover:bg-red-100"
                                @click="removeTableRow"
                            >
                                Remove row
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md border border-red-100 bg-red-50 px-3 py-1.5 text-xs text-red-700 hover:bg-red-100"
                                @click="removeTableColumn"
                            >
                                Remove column
                            </button>
                        </div>

                        <div
                            class="overflow-x-auto rounded-md border border-neutral-200"
                        >
                            <table class="w-full border-collapse text-sm">
                                <tbody>
                                    <tr
                                        v-for="(row, rowIndex) in rows"
                                        :key="rowIndex"
                                    >
                                        <td
                                            v-for="(cell, colIndex) in row"
                                            :key="colIndex"
                                            class="border border-neutral-200 p-0"
                                        >
                                            <div
                                                class="min-w-52 space-y-1.5 bg-white p-2"
                                            >
                                                <textarea
                                                    v-model="
                                                        rows[rowIndex][colIndex]
                                                            .text
                                                    "
                                                    :name="`table_data[${rowIndex}][${colIndex}][text]`"
                                                    rows="2"
                                                    placeholder="Cell text"
                                                    class="w-full resize-y rounded border border-neutral-200 px-2 py-1.5 text-xs outline-none focus:border-neutral-400"
                                                />
                                                <input
                                                    v-model="
                                                        rows[rowIndex][colIndex]
                                                            .link_url
                                                    "
                                                    :name="`table_data[${rowIndex}][${colIndex}][link_url]`"
                                                    type="text"
                                                    placeholder="Optional link URL"
                                                    class="w-full rounded border border-neutral-200 px-2 py-1.5 text-xs outline-none focus:border-neutral-400"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <Button
                    type="submit"
                    variant="secondary"
                    class="w-fit"
                    :disabled="processing || !isDirty"
                >
                    Save block
                </Button>
            </Form>
        </div>
    </div>
</template>
