<script setup lang="ts">
import { Form, Head, useForm } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, Plus, Trash2 } from '@lucide/vue';
import SettingController from '@/actions/App/Http/Controllers/Admin/SettingController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LazyImage from '@/components/LazyImage.vue';
import SocialIcon from '@/components/public/SocialIcon.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit as settingsEdit } from '@/routes/admin/settings';
import type { Setting, SocialIconName, SocialLink } from '@/types';

const props = defineProps<{
    setting: Setting;
}>();

const iconOptions: { value: SocialIconName; label: string }[] = [
    { value: 'instagram', label: 'Instagram' },
    { value: 'linkedin', label: 'LinkedIn' },
    { value: 'facebook', label: 'Facebook' },
    { value: 'youtube', label: 'YouTube' },
    { value: 'x', label: 'X' },
    { value: 'tiktok', label: 'TikTok' },
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'website', label: 'Website' },
];

const socialForm = useForm<{ social_links: SocialLink[] }>({
    social_links: (props.setting.social_links ?? []).map((link) => ({
        ...link,
    })),
});

function addSocialLink(): void {
    socialForm.social_links.push({ label: '', icon: 'instagram', url: '' });
    socialForm.clearErrors();
}

function removeSocialLink(index: number): void {
    socialForm.social_links.splice(index, 1);
    socialForm.clearErrors();
}

function moveSocialLink(index: number, direction: -1 | 1): void {
    const [link] = socialForm.social_links.splice(index, 1);

    if (link) {
        socialForm.social_links.splice(index + direction, 0, link);
    }

    socialForm.clearErrors();
}

function socialLinkError(
    index: number,
    field: keyof SocialLink,
): string | undefined {
    return (socialForm.errors as Record<string, string>)[
        `social_links.${index}.${field}`
    ];
}

function saveSocialLinks(): void {
    socialForm.put(SettingController.updateSocialLinks.url(), {
        preserveScroll: true,
        onSuccess: () => socialForm.defaults(),
    });
}

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
                    Sent as an attachment when a visitor requests the catalogue.
                    PDF, up to 10 MB.
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

        <section
            class="flex max-w-4xl flex-col gap-5 rounded-xl border bg-card p-6"
        >
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">
                        Footer social links
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Add links to show them in the footer. Use the arrows to
                        change their order.
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    :disabled="socialForm.social_links.length >= 12"
                    @click="addSocialLink"
                >
                    <Plus class="size-4" />
                    Add link
                </Button>
            </div>

            <p
                v-if="socialForm.social_links.length === 0"
                class="rounded-lg border border-dashed p-6 text-sm text-muted-foreground"
            >
                No social links are shown in the footer. Add one to get started.
            </p>

            <div v-else class="flex flex-col gap-4">
                <div
                    v-for="(link, index) in socialForm.social_links"
                    :key="index"
                    class="rounded-lg border bg-background p-4"
                >
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div
                            class="flex items-center gap-3 text-sm font-medium"
                        >
                            <span
                                class="flex size-9 items-center justify-center rounded-full border text-foreground"
                            >
                                <SocialIcon :icon="link.icon" />
                            </span>
                            Link {{ index + 1 }}
                        </div>
                        <div class="flex items-center gap-1">
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="index === 0"
                                :aria-label="`Move link ${index + 1} up`"
                                @click="moveSocialLink(index, -1)"
                            >
                                <ArrowUp class="size-4" />
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon-sm"
                                :disabled="
                                    index === socialForm.social_links.length - 1
                                "
                                :aria-label="`Move link ${index + 1} down`"
                                @click="moveSocialLink(index, 1)"
                            >
                                <ArrowDown class="size-4" />
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon-sm"
                                :aria-label="`Remove link ${index + 1}`"
                                @click="removeSocialLink(index)"
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label :for="`social-icon-${index}`">Icon</Label>
                            <select
                                :id="`social-icon-${index}`"
                                v-model="link.icon"
                                class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                            >
                                <option
                                    v-for="option in iconOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <InputError
                                :message="socialLinkError(index, 'icon')"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label :for="`social-label-${index}`"
                                >Link name</Label
                            >
                            <Input
                                :id="`social-label-${index}`"
                                v-model="link.label"
                                placeholder="e.g. Instagram"
                                maxlength="50"
                            />
                            <InputError
                                :message="socialLinkError(index, 'label')"
                            />
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2">
                        <Label :for="`social-url-${index}`">Profile URL</Label>
                        <Input
                            :id="`social-url-${index}`"
                            v-model="link.url"
                            type="url"
                            placeholder="https://..."
                            required
                        />
                        <InputError :message="socialLinkError(index, 'url')" />
                    </div>
                </div>
            </div>

            <InputError :message="socialForm.errors.social_links" />
            <div class="flex flex-wrap items-center gap-4 border-t pt-5">
                <Button
                    type="button"
                    size="lg"
                    :disabled="socialForm.processing || !socialForm.isDirty"
                    @click="saveSocialLinks"
                >
                    {{
                        socialForm.processing ? 'Saving…' : 'Save social links'
                    }}
                </Button>
                <p
                    v-if="socialForm.recentlySuccessful"
                    class="text-sm text-emerald-700"
                >
                    Social links saved.
                </p>
            </div>
        </section>
    </div>
</template>
