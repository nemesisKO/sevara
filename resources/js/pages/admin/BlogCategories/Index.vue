<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import BlogCategoryController from '@/actions/App/Http/Controllers/Admin/BlogCategoryController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as blogCategoriesIndex } from '@/routes/admin/blog-categories';
import type { BlogCategory } from '@/types';

defineProps<{
    categories: BlogCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Blog categories', href: blogCategoriesIndex() },
        ],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this category?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Blog categories" />

    <div class="flex flex-col space-y-8">
        <Heading
            variant="small"
            title="Blog categories"
            description="Manage the categories blog posts can be assigned to"
        />

        <Form
            :options="{ preserveScroll: true }"
            v-bind="BlogCategoryController.store.form()"
            reset-on-success
            class="flex max-w-md flex-col gap-5 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    placeholder="Export Guide"
                    required
                />
                <InputError :message="errors.name" />
            </div>

            <Button
                type="submit"
                size="lg"
                :disabled="processing"
                class="w-fit"
            >
                Add category
            </Button>
        </Form>

        <div
            v-if="categories.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No categories yet. Add one above before creating blog posts.
        </div>

        <div
            v-else
            class="flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl border bg-card"
        >
            <div
                v-for="category in categories"
                :key="category.id"
                class="flex items-center justify-between gap-3 p-4"
            >
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">
                        {{ category.name }}
                    </p>
                    <p class="truncate text-sm text-muted-foreground">
                        {{ category.blogs_count ?? 0 }} blog post{{
                            category.blogs_count === 1 ? '' : 's'
                        }}
                    </p>
                </div>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogCategoryController.destroy.form(category.id)"
                    v-slot="{ processing }"
                >
                    <Button
                        type="submit"
                        variant="ghost"
                        size="icon-sm"
                        :disabled="processing"
                        aria-label="Delete category"
                        @click="confirmDelete"
                    >
                        <Trash2 />
                    </Button>
                </Form>
            </div>
        </div>
    </div>
</template>
