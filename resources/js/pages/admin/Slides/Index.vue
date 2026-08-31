<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import SlideController from '@/actions/App/Http/Controllers/Admin/SlideController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as slidesIndex } from '@/routes/admin/slides';
import type { Slide } from '@/types';

defineProps<{
    slides: Slide[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Slider',
                href: slidesIndex(),
            },
        ],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this slide?')) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Slider" />

    <div class="flex flex-col space-y-8 px-4">
        <Heading
            variant="small"
            title="Slider"
            description="Add, edit, or remove homepage hero slides and their call-to-action buttons"
        />

        <Form
            :options="{ preserveScroll: true }"
            v-bind="SlideController.store.form()"
            reset-on-success
            class="flex max-w-2xl flex-col gap-5 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="image">Image</Label>
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
                <Label for="link">Slide link</Label>
                <Input
                    id="link"
                    name="link"
                    placeholder="https://... or /private-label"
                    required
                />
                <InputError :message="errors.link" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="primary_cta_text">First button text</Label>
                    <Input
                        id="primary_cta_text"
                        name="primary_cta_text"
                        placeholder="Explore products"
                    />
                    <InputError :message="errors.primary_cta_text" />
                </div>
                <div class="grid gap-2">
                    <Label for="primary_cta_link">First button link</Label>
                    <Input
                        id="primary_cta_link"
                        name="primary_cta_link"
                        placeholder="/products"
                    />
                    <InputError :message="errors.primary_cta_link" />
                </div>
                <div class="grid gap-2">
                    <Label for="secondary_cta_text">Second button text</Label>
                    <Input
                        id="secondary_cta_text"
                        name="secondary_cta_text"
                        placeholder="Contact export team"
                    />
                    <InputError :message="errors.secondary_cta_text" />
                </div>
                <div class="grid gap-2">
                    <Label for="secondary_cta_link">Second button link</Label>
                    <Input
                        id="secondary_cta_link"
                        name="secondary_cta_link"
                        placeholder="/contact"
                    />
                    <InputError :message="errors.secondary_cta_link" />
                </div>
            </div>

            <Button type="submit" size="lg" :disabled="processing">
                Add slide
            </Button>
        </Form>

        <div
            v-if="slides.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No slides yet. Add one above to populate the homepage slider.
        </div>

        <div v-else class="grid gap-5 sm:grid-cols-2">
            <div
                v-for="slide in slides"
                :key="slide.id"
                class="overflow-hidden rounded-xl border bg-card"
            >
                <LazyImage
                    :src="slide.image_url"
                    alt=""
                    class="aspect-video w-full object-cover"
                />
                <div class="space-y-4 p-4">
                    <Form
                        :options="{ preserveScroll: true }"
                        v-bind="SlideController.update.form(slide.id)"
                        v-slot="{ errors, processing }"
                        class="grid gap-3"
                    >
                        <div class="grid gap-1.5">
                            <Label :for="`link-${slide.id}`">Slide link</Label>
                            <Input
                                :id="`link-${slide.id}`"
                                name="link"
                                :default-value="slide.link"
                                required
                            />
                            <InputError :message="errors.link" />
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label :for="`primary_cta_text-${slide.id}`"
                                    >First button text</Label
                                >
                                <Input
                                    :id="`primary_cta_text-${slide.id}`"
                                    name="primary_cta_text"
                                    :default-value="
                                        slide.primary_cta_text ?? ''
                                    "
                                />
                                <InputError
                                    :message="errors.primary_cta_text"
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <Label :for="`primary_cta_link-${slide.id}`"
                                    >First button link</Label
                                >
                                <Input
                                    :id="`primary_cta_link-${slide.id}`"
                                    name="primary_cta_link"
                                    :default-value="
                                        slide.primary_cta_link ?? ''
                                    "
                                />
                                <InputError
                                    :message="errors.primary_cta_link"
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <Label :for="`secondary_cta_text-${slide.id}`"
                                    >Second button text</Label
                                >
                                <Input
                                    :id="`secondary_cta_text-${slide.id}`"
                                    name="secondary_cta_text"
                                    :default-value="
                                        slide.secondary_cta_text ?? ''
                                    "
                                />
                                <InputError
                                    :message="errors.secondary_cta_text"
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <Label :for="`secondary_cta_link-${slide.id}`"
                                    >Second button link</Label
                                >
                                <Input
                                    :id="`secondary_cta_link-${slide.id}`"
                                    name="secondary_cta_link"
                                    :default-value="
                                        slide.secondary_cta_link ?? ''
                                    "
                                />
                                <InputError
                                    :message="errors.secondary_cta_link"
                                />
                            </div>
                        </div>
                        <Button type="submit" :disabled="processing"
                            >Save buttons</Button
                        >
                    </Form>
                    <div class="flex items-center justify-between gap-2">
                        <a
                            :href="slide.link"
                            target="_blank"
                            rel="noopener"
                            class="truncate text-sm text-muted-foreground hover:text-foreground"
                        >
                            View slide link
                        </a>
                        <Form
                            :options="{ preserveScroll: true }"
                            v-bind="SlideController.destroy.form(slide.id)"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon"
                                :disabled="processing"
                                aria-label="Delete slide"
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
