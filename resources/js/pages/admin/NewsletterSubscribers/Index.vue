<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import NewsletterSubscriberController from '@/actions/App/Http/Controllers/Admin/NewsletterSubscriberController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index as subscribersIndex } from '@/routes/admin/newsletter-subscribers';
import type { NewsletterSubscriber } from '@/types';

defineProps<{
    subscribers: NewsletterSubscriber[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Newsletter subscribers', href: subscribersIndex() },
        ],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Remove this newsletter subscriber?')) {
        event.preventDefault();
    }
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}
</script>

<template>
    <Head title="Newsletter subscribers" />

    <div class="flex max-w-4xl flex-col space-y-8 bg-white px-6 py-8 lg:px-10">
        <Heading
            variant="small"
            title="Newsletter subscribers"
            description="People who subscribed through the Stay Updated form in the public footer."
        />

        <div
            v-if="subscribers.length === 0"
            class="rounded-xl border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
        >
            No subscribers yet.
        </div>

        <div v-else class="overflow-hidden rounded-xl border">
            <div
                v-for="subscriber in subscribers"
                :key="subscriber.id"
                class="flex items-center justify-between gap-4 border-b px-5 py-4 last:border-b-0"
            >
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-neutral-900">
                        {{ subscriber.email }}
                    </p>
                    <p class="text-xs text-neutral-500">
                        Subscribed {{ formatDate(subscriber.created_at) }}
                    </p>
                </div>

                <Form
                    :options="{ preserveScroll: true }"
                    v-bind="
                        NewsletterSubscriberController.destroy.form(
                            subscriber.id,
                        )
                    "
                    v-slot="{ processing }"
                >
                    <Button
                        type="submit"
                        variant="ghost"
                        size="icon-sm"
                        :disabled="processing"
                        aria-label="Remove subscriber"
                        @click="confirmDelete"
                    >
                        <Trash2 />
                    </Button>
                </Form>
            </div>
        </div>
    </div>
</template>
