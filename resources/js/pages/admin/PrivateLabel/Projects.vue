<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import PrivateLabelProjectController from '@/actions/App/Http/Controllers/Admin/PrivateLabelProjectController';
import Heading from '@/components/Heading.vue';
import { index as privateLabelIndex } from '@/routes/admin/private-label';
import { index as projectsIndex } from '@/routes/admin/private-label/projects';
import type { PrivateLabelProject } from '@/types';

defineProps<{ projects: PrivateLabelProject[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Private Label', href: privateLabelIndex() },
            { title: 'Projects', href: projectsIndex() },
        ],
    },
});

const fileGroupLabels: Record<string, string> = {
    product_files: 'Product references',
    brand_files: 'Brand assets',
    packaging_files: 'Packaging artwork',
    compliance_files: 'Compliance documents',
};

function formatDate(value: string): string {
    return new Date(value).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}

function formatValue(value: unknown): string {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    if (Array.isArray(value)) {
        return (
            value
                .map((item) =>
                    typeof item === 'object'
                        ? JSON.stringify(item)
                        : String(item),
                )
                .join(', ') || '—'
        );
    }

    return typeof value === 'object' ? JSON.stringify(value) : String(value);
}

function formatLabel(key: string): string {
    return key.replaceAll('_', ' ');
}
</script>

<template>
    <Head title="Private Label Projects" />

    <div class="flex max-w-5xl flex-col gap-8 bg-white px-6 py-8 lg:px-10">
        <Heading
            variant="small"
            title="Private label projects"
            description="Detailed project briefs submitted from the Kaveh form."
        />

        <p
            v-if="projects.length === 0"
            class="rounded-xl border border-dashed border-neutral-300 p-10 text-center text-sm text-neutral-500"
        >
            No projects yet.
        </p>

        <div v-else class="flex flex-col gap-4">
            <article
                v-for="project in projects"
                :key="project.id"
                class="rounded-xl border border-neutral-200 bg-white p-5 sm:p-6"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-neutral-900">
                            {{ project.reference }}
                        </p>
                        <h2 class="mt-1 text-lg font-medium text-neutral-900">
                            {{ project.company_name }}
                        </h2>
                        <p class="text-sm text-neutral-600">
                            {{ project.full_name }} ·
                            <a
                                :href="'mailto:' + project.email"
                                class="underline-offset-2 hover:underline"
                                >{{ project.email }}</a
                            >
                        </p>
                    </div>
                    <time
                        class="text-xs text-neutral-500"
                        :datetime="project.created_at"
                    >
                        {{ formatDate(project.created_at) }}
                    </time>
                </div>

                <details class="mt-5 border-t border-neutral-200 pt-4">
                    <summary
                        class="cursor-pointer text-sm font-semibold text-neutral-900"
                    >
                        View project details and files
                    </summary>
                    <dl class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                        <div
                            v-for="[key, value] in Object.entries(
                                project.details,
                            )"
                            :key="key"
                        >
                            <dt
                                class="text-xs font-medium tracking-wide text-neutral-500 capitalize"
                            >
                                {{ formatLabel(key) }}
                            </dt>
                            <dd
                                class="mt-1 text-sm break-words whitespace-pre-line text-neutral-900"
                            >
                                {{ formatValue(value) }}
                            </dd>
                        </div>
                    </dl>

                    <div
                        v-for="[group, files] in Object.entries(
                            project.files ?? {},
                        )"
                        :key="group"
                        class="mt-6 border-t border-neutral-200 pt-4"
                    >
                        <h3 class="text-sm font-semibold text-neutral-900">
                            {{ fileGroupLabels[group] ?? formatLabel(group) }}
                        </h3>
                        <ul class="mt-2 flex flex-col gap-2">
                            <li v-for="(file, index) in files" :key="index">
                                <a
                                    :href="
                                        PrivateLabelProjectController.download.url(
                                            {
                                                project: project.id,
                                                group,
                                                index,
                                            },
                                        )
                                    "
                                    class="inline-flex items-center gap-2 text-sm text-blue-700 underline-offset-2 hover:underline"
                                >
                                    <Download class="size-4" />
                                    {{ file.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </details>
            </article>
        </div>
    </div>
</template>
