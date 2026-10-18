<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { MailOpen, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import SampleRequestController from '@/actions/App/Http/Controllers/Admin/SampleRequestController';
import Heading from '@/components/Heading.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { index as sampleRequestsIndex } from '@/routes/admin/sample-requests';
import type { SampleRequest } from '@/types';

const props = defineProps<{
    sampleRequests: SampleRequest[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Sample Requests', href: sampleRequestsIndex() },
        ],
    },
});

const unreadCount = computed(
    () => props.sampleRequests.filter((request) => !request.read_at).length,
);

function confirmDelete(event: MouseEvent): void {
    if (!confirm('Delete this sample request?')) {
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
    <div class="flex max-w-5xl flex-col gap-8 px-6 py-8 lg:px-10">
        <Head title="Sample Requests" />
        <div class="flex flex-wrap items-end justify-between gap-4">
            <Heading
                variant="small"
                title="Sample requests"
                description="Requests submitted from individual product pages."
            />
            <p
                v-if="unreadCount > 0"
                class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900"
            >
                {{ unreadCount }} new
            </p>
        </div>

        <div
            v-if="sampleRequests.length === 0"
            class="rounded-xl border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
        >
            No sample requests yet.
        </div>

        <div v-else class="flex flex-col gap-4">
            <article
                v-for="request in sampleRequests"
                :key="request.id"
                class="rounded-xl border p-6"
                :class="
                    request.read_at
                        ? 'border-neutral-200 bg-white'
                        : 'border-amber-300 bg-amber-50/30'
                "
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-semibold text-neutral-900">
                                {{ request.product_name }}
                            </h2>
                            <span
                                v-if="!request.read_at"
                                class="rounded-full bg-amber-700 px-2 py-0.5 text-xs font-medium text-white"
                            >
                                New
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-neutral-500">
                            Requested by {{ request.full_name }} ·
                            {{ formatDate(request.created_at) }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            preserve-scroll
                            :href="
                                SampleRequestController.markAsRead(request.id)
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
                            {{ request.read_at ? 'Mark unread' : 'Mark read' }}
                        </Link>
                        <Form
                            v-bind="
                                SampleRequestController.destroy.form(request.id)
                            "
                            :options="{ preserveScroll: true }"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="processing"
                                aria-label="Delete sample request"
                                @click="confirmDelete"
                            >
                                <Trash2 />
                            </Button>
                        </Form>
                    </div>
                </div>

                <dl
                    class="mt-5 grid gap-4 border-t border-neutral-200 pt-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div>
                        <dt
                            class="text-xs font-medium text-neutral-500 uppercase"
                        >
                            Sample volume
                        </dt>
                        <dd class="mt-1 font-medium text-neutral-900">
                            {{ request.sample_volume }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-medium text-neutral-500 uppercase"
                        >
                            Shipping terms
                        </dt>
                        <dd class="mt-1 font-medium text-neutral-900">
                            {{ request.shipping_term }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-medium text-neutral-500 uppercase"
                        >
                            Email
                        </dt>
                        <dd class="mt-1 text-sm break-all text-neutral-900">
                            <a
                                :href="`mailto:${request.email}`"
                                class="underline-offset-2 hover:underline"
                            >
                                {{ request.email }}
                            </a>
                        </dd>
                    </div>
                    <div v-if="request.phone">
                        <dt
                            class="text-xs font-medium text-neutral-500 uppercase"
                        >
                            Phone
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            <a
                                :href="`tel:${request.phone}`"
                                class="underline-offset-2 hover:underline"
                            >
                                {{ request.phone }}
                            </a>
                        </dd>
                    </div>
                    <div v-if="request.company_name">
                        <dt
                            class="text-xs font-medium text-neutral-500 uppercase"
                        >
                            Company
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ request.company_name }}
                        </dd>
                    </div>
                </dl>

                <div
                    v-if="request.description"
                    class="mt-5 border-t border-neutral-200 pt-5"
                >
                    <p class="text-xs font-medium text-neutral-500 uppercase">
                        Requirements
                    </p>
                    <p
                        class="mt-2 text-sm whitespace-pre-line text-neutral-700"
                    >
                        {{ request.description }}
                    </p>
                </div>
            </article>
        </div>
    </div>
</template>
