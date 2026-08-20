<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { MailOpen, Trash2 } from '@lucide/vue';
import PrivateLabelInquiryController from '@/actions/App/Http/Controllers/Admin/PrivateLabelInquiryController';
import Heading from '@/components/Heading.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { index as privateLabelIndex } from '@/routes/admin/private-label';
import { index as inquiriesIndex } from '@/routes/admin/private-label/inquiries';
import type { PrivateLabelInquiry } from '@/types';

defineProps<{
    inquiries: PrivateLabelInquiry[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Private Label', href: privateLabelIndex() },
            { title: 'Inquiries', href: inquiriesIndex() },
        ],
    },
});

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this inquiry?')) {
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
    <Head title="Private Label Inquiries" />

    <div class="flex max-w-4xl flex-col space-y-8 bg-white px-6 py-8 lg:px-10">
        <Heading
            variant="small"
            title="Private label inquiries"
            description="Messages sent from the form on the public Private Label page."
        />

        <div
            v-if="inquiries.length === 0"
            class="rounded-xl border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
        >
            No inquiries yet.
        </div>

        <div v-else class="flex flex-col gap-4">
            <article
                v-for="inquiry in inquiries"
                :key="inquiry.id"
                class="rounded-xl border bg-white p-6"
                :class="
                    inquiry.read_at
                        ? 'border-neutral-200'
                        : 'border-neutral-900/20 bg-neutral-50'
                "
            >
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-base font-medium text-neutral-900">
                            {{ inquiry.full_name }}
                            <span
                                v-if="!inquiry.read_at"
                                class="ml-1 rounded-full bg-neutral-900 px-2 py-0.5 text-xs font-normal text-white"
                            >
                                New
                            </span>
                        </p>
                        <p class="text-sm text-neutral-500">
                            <a
                                :href="`mailto:${inquiry.email}`"
                                class="underline-offset-2 hover:text-neutral-900 hover:underline"
                            >
                                {{ inquiry.email }}
                            </a>
                            &middot;
                            {{ formatDate(inquiry.created_at) }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            preserve-scroll
                            :href="
                                PrivateLabelInquiryController.markAsRead(
                                    inquiry.id,
                                )
                            "
                            method="patch"
                            as="button"
                            :class="
                                buttonVariants({
                                    variant: 'outline',
                                    size: 'sm',
                                })
                            "
                        >
                            <MailOpen />
                            {{
                                inquiry.read_at
                                    ? 'Mark as unread'
                                    : 'Mark as read'
                            }}
                        </Link>
                        <Form
                            :options="{ preserveScroll: true }"
                            v-bind="
                                PrivateLabelInquiryController.destroy.form(
                                    inquiry.id,
                                )
                            "
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="processing"
                                aria-label="Delete inquiry"
                                @click="confirmDelete"
                            >
                                <Trash2 />
                            </Button>
                        </Form>
                    </div>
                </div>

                <dl
                    class="mt-5 grid gap-4 border-t border-neutral-200 pt-5 sm:grid-cols-3"
                >
                    <div>
                        <dt
                            class="text-xs font-medium tracking-wide text-neutral-500 uppercase"
                        >
                            Country / market
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ inquiry.country }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-medium tracking-wide text-neutral-500 uppercase"
                        >
                            Product format
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ inquiry.product_format }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-medium tracking-wide text-neutral-500 uppercase"
                        >
                            Estimated quantity
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ inquiry.estimated_quantity }}
                        </dd>
                    </div>
                </dl>

                <p
                    v-if="inquiry.message"
                    class="mt-5 border-t border-neutral-200 pt-5 text-sm whitespace-pre-line text-neutral-700"
                >
                    {{ inquiry.message }}
                </p>
            </article>
        </div>
    </div>
</template>
