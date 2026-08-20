<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SettingController from '@/actions/App/Http/Controllers/Admin/SettingController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { edit as settingsEdit } from '@/routes/admin/settings';
import type { Setting } from '@/types';

defineProps<{
    setting: Setting;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Settings',
                href: settingsEdit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Settings" />

    <div class="flex flex-col space-y-8 p-6">
        <Heading
            variant="small"
            title="Settings"
            description="Manage general website settings"
        />

        <Form
            :options="{ preserveScroll: true }"
            v-bind="SettingController.update.form()"
            class="flex max-w-md flex-col gap-5 rounded-xl border bg-card p-6"
            v-slot="{ errors, processing, isDirty }"
        >
            <div class="grid gap-2">
                <Label for="logo">Website logo</Label>
                <div class="flex items-center gap-4">
                    <div
                        v-if="setting.logo_url"
                        class="flex h-16 w-32 items-center justify-center rounded-md border bg-white p-2"
                    >
                        <LazyImage
                            :src="setting.logo_url"
                            alt="Current logo"
                            class="max-h-full max-w-full object-contain"
                        />
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        No logo uploaded yet.
                    </p>
                </div>
                <input
                    id="logo"
                    type="file"
                    name="logo"
                    accept="image/*"
                    class="h-10 w-full rounded-md border border-input bg-transparent text-sm file:mr-3 file:h-10 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground"
                />
                <p class="text-xs text-muted-foreground">
                    Shown in the site header and footer. Recommended:
                    transparent PNG or SVG.
                </p>
                <InputError :message="errors.logo" />
            </div>

            <div class="grid gap-2 border-t pt-5">
                <Label for="catalogue">Catalogue PDF</Label>
                <a
                    v-if="setting.catalogue_url"
                    :href="setting.catalogue_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-fit text-sm font-medium text-primary underline-offset-4 hover:underline"
                >
                    View current catalogue
                </a>
                <p v-else class="text-sm text-muted-foreground">
                    No catalogue uploaded yet.
                </p>
                <input
                    id="catalogue"
                    type="file"
                    name="catalogue"
                    accept="application/pdf,.pdf"
                    class="h-10 w-full rounded-md border border-input bg-transparent text-sm file:mr-3 file:h-10 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground"
                />
                <p class="text-xs text-muted-foreground">
                    Sent as an attachment when a visitor requests the catalogue. PDF, up to 10 MB.
                </p>
                <InputError :message="errors.catalogue" />
            </div>

            <Button
                type="submit"
                size="lg"
                class="w-fit"
                :disabled="processing || !isDirty"
            >
                Save settings
            </Button>
        </Form>
    </div>
</template>
