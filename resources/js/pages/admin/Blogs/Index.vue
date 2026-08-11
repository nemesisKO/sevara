<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import BlogController from '@/actions/App/Http/Controllers/Admin/BlogController';
import Heading from '@/components/Heading.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    create as createBlog,
    index as blogsIndex,
} from '@/routes/admin/blogs';
import type { Blog } from '@/types';

defineProps<{
    blogs: Blog[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Blog', href: blogsIndex() }],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this blog post? This cannot be undone.')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Blog" />

    <div class="flex flex-col space-y-8">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Blog"
                description="Manage the blog posts shown on the website"
            />
            <Link :href="createBlog()" :class="buttonVariants({ size: 'sm' })">
                <Plus />
                Add blog post
            </Link>
        </div>

        <div
            v-if="blogs.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No blog posts yet. Add one to get started.
        </div>

        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="blog in blogs"
                :key="blog.id"
                class="overflow-hidden rounded-xl border bg-card"
            >
                <div
                    class="flex aspect-video items-center justify-center bg-white"
                >
                    <LazyImage
                        v-if="blog.image_url"
                        :src="blog.image_url"
                        alt=""
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-xs text-muted-foreground"
                        >No image</span
                    >
                </div>
                <div class="flex items-start justify-between gap-2 p-4">
                    <div class="min-w-0">
                        <p
                            v-if="blog.category"
                            class="truncate text-xs font-medium text-muted-foreground uppercase"
                        >
                            {{ blog.category.name }}
                        </p>
                        <p class="truncate text-sm font-medium">
                            {{ blog.title }}
                        </p>
                        <p class="truncate text-sm text-muted-foreground">
                            /blog/{{ blog.slug }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            :href="BlogController.edit(blog.id)"
                            :class="
                                buttonVariants({ variant: 'ghost', size: 'sm' })
                            "
                        >
                            Edit
                        </Link>
                        <Form
                            :options="{ preserveScroll: true }"
                            v-bind="BlogController.destroy.form(blog.id)"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="processing"
                                aria-label="Delete blog post"
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
