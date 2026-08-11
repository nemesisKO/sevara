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
            description="Add or remove the images shown in the homepage hero slider"
        />

        <Form
            :options="{ preserveScroll: true }"
            v-bind="SlideController.store.form()"
            reset-on-success
            class="flex max-w-md flex-col gap-5 rounded-xl border bg-card p-6"
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
                <Label for="link">Link</Label>
                <Input
                    id="link"
                    name="link"
                    placeholder="https://... or /private-label"
                    required
                />
                <InputError :message="errors.link" />
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

        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
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
                <div class="flex items-center justify-between gap-2 p-4">
                    <a
                        :href="slide.link"
                        target="_blank"
                        rel="noopener"
                        class="truncate text-sm text-muted-foreground hover:text-foreground"
                    >
                        {{ slide.link }}
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
</template>
