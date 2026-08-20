<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BlogController from '@/actions/App/Http/Controllers/Admin/BlogController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as blogsIndex } from '@/routes/admin/blogs';
import type { BlogCategory } from '@/types';

defineProps<{
    categories: BlogCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Blog', href: blogsIndex() },
            { title: 'Add blog post', href: '' },
        ],
    },
});

const selectClass =
    'h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50';
</script>

<template>
    <Head title="Add blog post" />

    <div class="flex max-w-2xl flex-col space-y-8">
        <Heading
            variant="small"
            title="Add blog post"
            description="Create the post. You can add the cover image, content blocks, tags and FAQs after saving."
        />

        <Form
            v-bind="BlogController.store.form()"
            class="flex flex-col gap-6 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    placeholder="The Future of Chocolate Dates in Global Retail"
                    required
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="slug">URL slug</Label>
                <Input
                    id="slug"
                    name="slug"
                    placeholder="future-of-chocolate-dates-in-global-retail"
                    required
                />
                <p class="text-xs text-muted-foreground">
                    Used in the page address, e.g.
                    /blog/future-of-chocolate-dates-in-global-retail
                </p>
                <InputError :message="errors.slug" />
            </div>

            <div class="grid gap-2">
                <Label for="blog_category_id">Category</Label>
                <select
                    id="blog_category_id"
                    name="blog_category_id"
                    required
                    :class="selectClass"
                >
                    <option value="" disabled selected>
                        Select a category
                    </option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
                <InputError :message="errors.blog_category_id" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="author">Author</Label>
                    <Input
                        id="author"
                        name="author"
                        placeholder="Sevara Editorial Team"
                    />
                    <InputError :message="errors.author" />
                </div>
                <div class="grid gap-2">
                    <Label for="source_link">Source link</Label>
                    <Input
                        id="source_link"
                        name="source_link"
                        placeholder="https://..."
                    />
                    <InputError :message="errors.source_link" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="custom_date">Published date</Label>
                    <Input
                        id="custom_date"
                        name="custom_date"
                        placeholder="Autumn 2026"
                    />
                    <InputError :message="errors.custom_date" />
                </div>
                <div class="grid gap-2">
                    <Label for="read_duration">Read duration (minutes)</Label>
                    <Input
                        id="read_duration"
                        name="read_duration"
                        placeholder="7"
                    />
                    <InputError :message="errors.read_duration" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="mini_review">Short summary</Label>
                <Textarea
                    id="mini_review"
                    name="mini_review"
                    placeholder="Shown at the top of the post and in blog cards."
                />
                <InputError :message="errors.mini_review" />
            </div>

            <div class="grid gap-2">
                <Label for="meta_description">Meta description</Label>
                <Textarea
                    id="meta_description"
                    name="meta_description"
                    placeholder="Used for SEO and social sharing previews."
                />
                <InputError :message="errors.meta_description" />
            </div>

            <div class="grid gap-2">
                <Label for="image">Main picture (optional)</Label>
                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/*"
                    class="h-11 w-full rounded-md border border-input bg-transparent px-4 text-sm text-foreground file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground"
                />
                <p class="text-xs text-muted-foreground">
                    Included in the new-blog email when provided.
                </p>
                <InputError :message="errors.image" />
            </div>

            <Button
                type="submit"
                size="lg"
                class="w-fit"
                :disabled="processing"
            >
                Create blog post
            </Button>
        </Form>
    </div>
</template>
