<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, Plus, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import BlogBlockController from '@/actions/App/Http/Controllers/Admin/BlogBlockController';
import BlogController from '@/actions/App/Http/Controllers/Admin/BlogController';
import BlogTagController from '@/actions/App/Http/Controllers/Admin/BlogTagController';
import BlogBlockEditor from '@/components/admin/BlogBlockEditor.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { index as blogsIndex } from '@/routes/admin/blogs';
import { show as viewBlog } from '@/routes/blog';
import type { Blog, BlogCategory, BlogFaq, Tag } from '@/types';

const props = defineProps<{
    blog: Blog;
    categories: BlogCategory[];
    allTags: Tag[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Blog', href: blogsIndex() },
            { title: 'Edit blog post', href: '' },
        ],
    },
});

const selectClass =
    'h-11 w-full rounded-md border border-neutral-300 bg-white px-3 py-1 text-base shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm';

const references = ref<string[]>(
    props.blog.references ? [...props.blog.references] : [],
);

function addReference(): void {
    references.value.push('');
}

function removeReference(index: number): void {
    references.value.splice(index, 1);
}

const faqs = ref<BlogFaq[]>(
    props.blog.faqs ? props.blog.faqs.map((faq) => ({ ...faq })) : [],
);

function addFaq(): void {
    faqs.value.push({ question: '', answer: '' });
}

function removeFaq(index: number): void {
    faqs.value.splice(index, 1);
}

function confirmDeleteBlog(event: MouseEvent): void {
    if (!confirm('Delete this blog post? This cannot be undone.')) {
        event.preventDefault();
    }
}

function confirmDeleteTag(event: MouseEvent): void {
    if (!confirm('Remove this tag?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head :title="`Edit ${blog.title}`" />

    <div class="flex max-w-4xl flex-col space-y-10 bg-white px-6 py-8 lg:px-10">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Edit blog post"
                description="Update the post content, cover image and layout blocks."
            />
            <a
                :href="viewBlog.url({ slug: blog.slug })"
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
                title="Post details"
                description="The core content, cover image, references and FAQs for this post."
            />

            <Form
                :options="{ preserveScroll: true }"
                v-bind="BlogController.update.form(blog.id)"
                class="mt-6 flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-8"
                v-slot="{ errors, processing, isDirty }"
            >
                <div class="grid gap-2">
                    <Label for="title" class="text-neutral-900">Title</Label>
                    <Input
                        id="title"
                        name="title"
                        :default-value="blog.title"
                        class="h-11 text-base"
                        required
                    />
                    <InputError :message="errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="slug" class="text-neutral-900">URL slug</Label>
                    <Input
                        id="slug"
                        name="slug"
                        :default-value="blog.slug"
                        class="h-11 text-base"
                        required
                    />
                    <p class="text-xs text-neutral-500">
                        /blog/{{ blog.slug }}
                    </p>
                    <InputError :message="errors.slug" />
                </div>

                <div class="grid gap-2">
                    <Label for="blog_category_id" class="text-neutral-900"
                        >Category</Label
                    >
                    <select
                        id="blog_category_id"
                        name="blog_category_id"
                        :value="blog.blog_category_id"
                        required
                        :class="selectClass"
                    >
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
                        <Label for="author" class="text-neutral-900"
                            >Author</Label
                        >
                        <Input
                            id="author"
                            name="author"
                            :default-value="blog.author ?? ''"
                            class="h-11 text-base"
                        />
                        <InputError :message="errors.author" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="source_link" class="text-neutral-900"
                            >Source link</Label
                        >
                        <Input
                            id="source_link"
                            name="source_link"
                            :default-value="blog.source_link ?? ''"
                            class="h-11 text-base"
                        />
                        <InputError :message="errors.source_link" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="custom_date" class="text-neutral-900"
                            >Published date</Label
                        >
                        <Input
                            id="custom_date"
                            name="custom_date"
                            :default-value="blog.custom_date ?? ''"
                            class="h-11 text-base"
                        />
                        <InputError :message="errors.custom_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="read_duration" class="text-neutral-900"
                            >Read duration (minutes)</Label
                        >
                        <Input
                            id="read_duration"
                            name="read_duration"
                            :default-value="blog.read_duration ?? ''"
                            class="h-11 text-base"
                        />
                        <InputError :message="errors.read_duration" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="mini_review" class="text-neutral-900"
                        >Short summary</Label
                    >
                    <Textarea
                        id="mini_review"
                        name="mini_review"
                        :default-value="blog.mini_review ?? ''"
                        class="text-base"
                    />
                    <InputError :message="errors.mini_review" />
                </div>

                <div class="grid gap-2">
                    <Label for="meta_description" class="text-neutral-900"
                        >Meta description</Label
                    >
                    <Textarea
                        id="meta_description"
                        name="meta_description"
                        :default-value="blog.meta_description ?? ''"
                        class="text-base"
                    />
                    <InputError :message="errors.meta_description" />
                </div>

                <div class="grid gap-2">
                    <Label for="image" class="text-neutral-900"
                        >Cover image</Label
                    >
                    <div class="flex items-center gap-3">
                        <LazyImage
                            v-if="blog.image_url"
                            :src="blog.image_url"
                            alt=""
                            class="h-16 w-24 shrink-0 rounded-md border border-neutral-200 object-cover"
                        />
                        <input
                            id="image"
                            type="file"
                            name="image"
                            accept="image/*"
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-4 text-sm text-neutral-900 file:mr-3 file:h-11 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-neutral-900"
                        />
                    </div>
                    <InputError :message="errors.image" />
                </div>

                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <Label class="text-neutral-900">References</Label>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-xs font-medium text-neutral-600 hover:text-neutral-900"
                            @click="addReference"
                        >
                            <Plus class="size-3.5" /> Add reference
                        </button>
                    </div>
                    <div
                        v-for="(reference, index) in references"
                        :key="index"
                        class="flex items-center gap-2"
                    >
                        <Input
                            v-model="references[index]"
                            name="references[]"
                            placeholder="https://example.com/article"
                            class="h-10"
                        />
                        <button
                            type="button"
                            class="shrink-0 rounded-md border border-red-100 bg-red-50 p-2 text-red-700 hover:bg-red-100"
                            aria-label="Remove reference"
                            @click="removeReference(index)"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                    <InputError :message="errors.references" />
                </div>

                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <Label class="text-neutral-900">FAQs</Label>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-xs font-medium text-neutral-600 hover:text-neutral-900"
                            @click="addFaq"
                        >
                            <Plus class="size-3.5" /> Add FAQ
                        </button>
                    </div>
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="rounded-md border border-neutral-200 bg-neutral-50 p-3"
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-medium text-neutral-500"
                                >FAQ {{ index + 1 }}</span
                            >
                            <button
                                type="button"
                                class="text-xs text-red-700 hover:text-red-800"
                                @click="removeFaq(index)"
                            >
                                Remove
                            </button>
                        </div>
                        <div class="grid gap-2">
                            <Input
                                v-model="faqs[index].question"
                                :name="`faqs[${index}][question]`"
                                placeholder="Question"
                                class="h-10 bg-white"
                            />
                            <Textarea
                                v-model="faqs[index].answer"
                                :name="`faqs[${index}][answer]`"
                                placeholder="Answer"
                                class="bg-white"
                                rows="2"
                            />
                        </div>
                    </div>
                    <InputError :message="errors.faqs" />
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
                title="Tags"
                description="Attach topical tags shown on the blog post."
            />

            <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-8">
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogTagController.store.form(blog.id)"
                    reset-on-success
                    class="flex flex-wrap items-end gap-3"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid min-w-56 flex-1 gap-2">
                        <Label for="tag_title" class="text-neutral-900"
                            >Add tag</Label
                        >
                        <input
                            id="tag_title"
                            name="title"
                            list="tag-suggestions"
                            placeholder="Export Guide"
                            class="h-11 w-full rounded-md border border-neutral-300 bg-white px-3 text-sm text-neutral-900 outline-none focus-visible:border-neutral-400"
                        />
                        <datalist id="tag-suggestions">
                            <option
                                v-for="tag in allTags"
                                :key="tag.id"
                                :value="tag.title"
                            />
                        </datalist>
                        <InputError :message="errors.title" />
                    </div>
                    <Button
                        type="submit"
                        variant="secondary"
                        :disabled="processing"
                    >
                        Add tag
                    </Button>
                </Form>

                <div
                    v-if="blog.tags.length === 0"
                    class="mt-6 rounded-lg border border-dashed border-neutral-300 p-6 text-center text-sm text-neutral-500"
                >
                    No tags yet.
                </div>

                <div v-else class="mt-6 flex flex-wrap gap-2">
                    <Form
                        :options="{ preserveScroll: true }"
                        v-for="tag in blog.tags"
                        :key="tag.id"
                        v-bind="
                            BlogTagController.destroy.form({
                                blog: blog.id,
                                tag: tag.id,
                            })
                        "
                        v-slot="{ processing }"
                    >
                        <button
                            type="submit"
                            :disabled="processing"
                            class="inline-flex items-center gap-2 rounded-full bg-neutral-100 px-3 py-1.5 text-xs text-neutral-700 transition-colors hover:bg-red-50 hover:text-red-700"
                            @click="confirmDeleteTag"
                        >
                            {{ tag.title }}
                            <X class="size-3.5" />
                        </button>
                    </Form>
                </div>
            </div>
        </div>

        <Separator />

        <div>
            <Heading
                variant="small"
                title="Content blocks"
                description="Build the post body from text, image, hint and table blocks. Reorder them with the arrows."
            />

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogBlockController.store.form(blog.id)"
                    class="rounded-xl border border-neutral-200 bg-white p-4"
                    v-slot="{ processing }"
                >
                    <input type="hidden" name="type" value="article" />
                    <p class="mb-3 text-sm font-medium text-neutral-900">
                        Text block
                    </p>
                    <Button
                        type="submit"
                        variant="outline"
                        size="sm"
                        :disabled="processing"
                    >
                        <Plus /> Add
                    </Button>
                </Form>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogBlockController.store.form(blog.id)"
                    class="rounded-xl border border-neutral-200 bg-white p-4"
                    v-slot="{ errors, processing }"
                >
                    <input type="hidden" name="type" value="image" />
                    <p class="mb-3 text-sm font-medium text-neutral-900">
                        Image block
                    </p>
                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        required
                        class="mb-3 w-full text-xs"
                    />
                    <InputError :message="errors.image" />
                    <Button
                        type="submit"
                        variant="outline"
                        size="sm"
                        :disabled="processing"
                    >
                        <Plus /> Add
                    </Button>
                </Form>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogBlockController.store.form(blog.id)"
                    class="rounded-xl border border-neutral-200 bg-white p-4"
                    v-slot="{ processing }"
                >
                    <input type="hidden" name="type" value="hint" />
                    <p class="mb-3 text-sm font-medium text-neutral-900">
                        Hint block
                    </p>
                    <Button
                        type="submit"
                        variant="outline"
                        size="sm"
                        :disabled="processing"
                    >
                        <Plus /> Add
                    </Button>
                </Form>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="BlogBlockController.store.form(blog.id)"
                    class="rounded-xl border border-neutral-200 bg-white p-4"
                    v-slot="{ errors, processing }"
                >
                    <input type="hidden" name="type" value="table" />
                    <p class="mb-3 text-sm font-medium text-neutral-900">
                        Table block
                    </p>
                    <div class="mb-3 grid grid-cols-2 gap-2">
                        <input
                            type="number"
                            name="rows"
                            min="1"
                            max="12"
                            value="3"
                            class="h-9 w-full rounded-md border border-neutral-300 px-2 text-sm"
                        />
                        <input
                            type="number"
                            name="columns"
                            min="1"
                            max="8"
                            value="3"
                            class="h-9 w-full rounded-md border border-neutral-300 px-2 text-sm"
                        />
                    </div>
                    <InputError :message="errors.rows" />
                    <Button
                        type="submit"
                        variant="outline"
                        size="sm"
                        :disabled="processing"
                    >
                        <Plus /> Add
                    </Button>
                </Form>
            </div>

            <div
                v-if="blog.blocks.length === 0"
                class="mt-6 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
            >
                No content blocks yet. Add one above to start building the post.
            </div>

            <div v-else class="mt-6 flex flex-col gap-4">
                <BlogBlockEditor
                    v-for="(block, index) in blog.blocks"
                    :key="block.id"
                    :blog="blog"
                    :block="block"
                    :index="index"
                    :total="blog.blocks.length"
                />
            </div>
        </div>

        <Separator />

        <div
            class="flex items-center justify-between gap-4 rounded-xl border border-red-200 bg-white p-6"
        >
            <div>
                <p class="text-sm font-medium text-neutral-900">
                    Delete this blog post
                </p>
                <p class="text-sm text-neutral-500">
                    This removes the post and all its content blocks
                    permanently.
                </p>
            </div>
            <Form
                v-bind="BlogController.destroy.form(blog.id)"
                v-slot="{ processing }"
            >
                <Button
                    type="submit"
                    variant="outline"
                    class="shrink-0 border-red-300 text-red-600 hover:bg-red-50 hover:text-red-700"
                    :disabled="processing"
                    @click="confirmDeleteBlog"
                >
                    <Trash2 />
                    Delete blog post
                </Button>
            </Form>
        </div>
    </div>
</template>
