<script setup lang="ts">
import { onBeforeUnmount, reactive, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { slideTextStyle } from '@/lib/slideTextStyle';
import type { Slide, SlideTextStyle } from '@/types/slide';

const props = defineProps<{
    slide?: Slide;
    errors: Record<string, string>;
    idPrefix: string;
}>();

type TextField = 'title' | 'description';
type Edge = 'top' | 'left' | 'right' | 'bottom';
type EditableStyle = Record<Edge | 'size' | 'weight', string | number> & {
    color: string;
};

const fields = [
    {
        key: 'title',
        label: 'Title',
        placeholder: 'A clear headline for this slide',
    },
    {
        key: 'description',
        label: 'Description',
        placeholder: 'Add a short supporting message',
    },
] as const;
const edges: Edge[] = ['top', 'left', 'right', 'bottom'];
const previewMode = ref<'desktop' | 'mobile'>('desktop');
const previewImages = reactive<{
    desktop: string | null;
    mobile: string | null;
}>({
    desktop: null,
    mobile: null,
});
const content = reactive({
    title: props.slide?.title ?? '',
    description: props.slide?.description ?? '',
});

function initialStyle(kind: TextField): EditableStyle {
    const saved = props.slide?.[`${kind}_style`] as
        SlideTextStyle | null | undefined;

    return {
        top: String(
            saved?.top ??
                (saved?.bottom == null ? (kind === 'title' ? 22 : 43) : ''),
        ),
        left: String(saved?.left ?? (saved?.right == null ? 12 : '')),
        right: String(saved?.right ?? ''),
        bottom: String(saved?.bottom ?? ''),
        size: String(saved?.size ?? (kind === 'title' ? 48 : 20)),
        weight: String(saved?.weight ?? (kind === 'title' ? 700 : 400)),
        color: saved?.color ?? '#ffffff',
    };
}

const styles = reactive<Record<TextField, EditableStyle>>({
    title: initialStyle('title'),
    description: initialStyle('description'),
});

function previewImage(kind: 'desktop' | 'mobile'): string | null {
    if (kind === 'desktop') {
        return previewImages.desktop ?? props.slide?.image_url ?? null;
    }

    return (
        previewImages.mobile ??
        (props.slide?.mobile_image_path
            ? props.slide.mobile_image_url
            : null) ??
        previewImages.desktop ??
        props.slide?.image_url ??
        null
    );
}

function selectImage(event: Event, kind: 'desktop' | 'mobile'): void {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (previewImages[kind]) {
        URL.revokeObjectURL(previewImages[kind]);
    }

    previewImages[kind] = file ? URL.createObjectURL(file) : null;
}

function colorInputValue(value: string | number): string {
    return /^#[0-9a-fA-F]{6}$/.test(String(value)) ? String(value) : '#ffffff';
}

onBeforeUnmount(() => {
    for (const image of Object.values(previewImages)) {
        if (image) {
            URL.revokeObjectURL(image);
        }
    }
});
</script>

<template>
    <div class="space-y-6">
        <section class="space-y-3">
            <div>
                <h3 class="text-sm font-semibold">Slide images</h3>
                <p class="text-xs text-muted-foreground">
                    Use a wide image for desktop and a portrait image for
                    phones. Mobile uses the desktop image until you upload one.
                </p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label :for="`${idPrefix}-image`"
                        >Desktop image
                        <span class="text-muted-foreground">(wide)</span></Label
                    >
                    <input
                        :id="`${idPrefix}-image`"
                        type="file"
                        name="image"
                        accept="image/*"
                        :required="!slide"
                        class="w-full rounded-md border border-input p-2 text-sm file:mr-2 file:border-0 file:bg-transparent file:font-medium"
                        @change="selectImage($event, 'desktop')"
                    />
                    <InputError :message="errors.image" />
                    <p v-if="slide" class="text-xs text-muted-foreground">
                        Leave empty to keep the current image.
                    </p>
                </div>
                <div class="grid gap-2">
                    <Label :for="`${idPrefix}-mobile-image`"
                        >Mobile image
                        <span class="text-muted-foreground"
                            >(portrait, optional)</span
                        ></Label
                    >
                    <input
                        :id="`${idPrefix}-mobile-image`"
                        type="file"
                        name="mobile_image"
                        accept="image/*"
                        class="w-full rounded-md border border-input p-2 text-sm file:mr-2 file:border-0 file:bg-transparent file:font-medium"
                        @change="selectImage($event, 'mobile')"
                    />
                    <InputError :message="errors.mobile_image" />
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold">Preview</h3>
                    <p class="text-xs text-muted-foreground">
                        Text positions are measured from the image edges.
                    </p>
                </div>
                <div
                    class="flex rounded-md border p-0.5 text-xs"
                    aria-label="Preview screen size"
                >
                    <button
                        type="button"
                        class="rounded px-3 py-1.5"
                        :class="
                            previewMode === 'desktop' &&
                            'bg-muted font-semibold'
                        "
                        @click="previewMode = 'desktop'"
                    >
                        Desktop
                    </button>
                    <button
                        type="button"
                        class="rounded px-3 py-1.5"
                        :class="
                            previewMode === 'mobile' && 'bg-muted font-semibold'
                        "
                        @click="previewMode = 'mobile'"
                    >
                        Mobile
                    </button>
                </div>
            </div>
            <div
                class="[container-type:inline-size] relative mx-auto overflow-hidden rounded-lg bg-[#0f1c3f] text-white"
                :class="
                    previewMode === 'mobile'
                        ? 'aspect-[4/5] w-full max-w-60'
                        : 'aspect-[21/9] w-full'
                "
            >
                <img
                    v-if="previewImage(previewMode)"
                    :src="previewImage(previewMode)!"
                    alt=""
                    class="h-full w-full object-cover"
                />
                <div
                    v-else
                    class="flex h-full items-center justify-center text-sm text-white/60"
                >
                    Upload an image to preview
                </div>
                <div
                    v-if="content.title"
                    class="absolute max-w-[76%] overflow-hidden leading-tight break-words whitespace-pre-line drop-shadow-lg"
                    :style="slideTextStyle(styles.title, 'title')"
                >
                    {{ content.title }}
                </div>
                <div
                    v-if="content.description"
                    class="absolute max-w-[76%] overflow-hidden leading-snug break-words whitespace-pre-line drop-shadow-lg"
                    :style="slideTextStyle(styles.description, 'description')"
                >
                    {{ content.description }}
                </div>
            </div>
        </section>

        <section
            v-for="field in fields"
            :key="field.key"
            class="space-y-4 rounded-lg border p-4"
        >
            <div>
                <h3 class="text-sm font-semibold">{{ field.label }}</h3>
                <p class="text-xs text-muted-foreground">
                    Leave the text empty to hide it on the slide.
                </p>
            </div>
            <div class="grid gap-2">
                <Label :for="`${idPrefix}-${field.key}`"
                    >{{ field.label }} text</Label
                >
                <Textarea
                    v-if="field.key === 'description'"
                    :id="`${idPrefix}-${field.key}`"
                    v-model="content.description"
                    name="description"
                    :placeholder="field.placeholder"
                    rows="2"
                />
                <Input
                    v-else
                    :id="`${idPrefix}-${field.key}`"
                    v-model="content.title"
                    name="title"
                    :placeholder="field.placeholder"
                />
                <InputError :message="errors[field.key]" />
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="grid gap-1.5">
                    <Label :for="`${idPrefix}-${field.key}-size`"
                        >Size (px)</Label
                    >
                    <Input
                        :id="`${idPrefix}-${field.key}-size`"
                        v-model="styles[field.key].size"
                        type="number"
                        min="12"
                        max="120"
                        :name="`${field.key}_style[size]`"
                    />
                    <InputError :message="errors[`${field.key}_style.size`]" />
                </div>
                <div class="grid gap-1.5">
                    <Label :for="`${idPrefix}-${field.key}-weight`"
                        >Boldness</Label
                    >
                    <select
                        :id="`${idPrefix}-${field.key}-weight`"
                        v-model="styles[field.key].weight"
                        :name="`${field.key}_style[weight]`"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    >
                        <option value="400">Regular</option>
                        <option value="500">Medium</option>
                        <option value="600">Semibold</option>
                        <option value="700">Bold</option>
                        <option value="800">Extra bold</option>
                        <option value="900">Black</option>
                    </select>
                    <InputError
                        :message="errors[`${field.key}_style.weight`]"
                    />
                </div>
                <div class="grid gap-1.5">
                    <Label :for="`${idPrefix}-${field.key}-color`"
                        >Color code</Label
                    >
                    <div class="flex gap-2">
                        <input
                            type="color"
                            :aria-label="`${field.label} color picker`"
                            :value="colorInputValue(styles[field.key].color)"
                            class="h-9 w-10 shrink-0 cursor-pointer rounded-md border border-input bg-transparent p-1"
                            @input="
                                styles[field.key].color = (
                                    $event.target as HTMLInputElement
                                ).value
                            "
                        />
                        <Input
                            :id="`${idPrefix}-${field.key}-color`"
                            v-model="styles[field.key].color"
                            :name="`${field.key}_style[color]`"
                            placeholder="#ffffff"
                            maxlength="7"
                        />
                    </div>
                    <InputError :message="errors[`${field.key}_style.color`]" />
                </div>
            </div>
            <div>
                <p class="mb-2 text-xs font-medium">Position (% from edge)</p>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div v-for="edge in edges" :key="edge" class="grid gap-1.5">
                        <Label
                            :for="`${idPrefix}-${field.key}-${edge}`"
                            class="capitalize"
                            >{{ edge }}</Label
                        >
                        <Input
                            :id="`${idPrefix}-${field.key}-${edge}`"
                            v-model="styles[field.key][edge]"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            :name="`${field.key}_style[${edge}]`"
                            placeholder="—"
                        />
                        <InputError
                            :message="errors[`${field.key}_style.${edge}`]"
                        />
                    </div>
                </div>
                <p class="mt-2 text-xs text-muted-foreground">
                    Use one vertical edge and one horizontal edge. Leave the
                    opposite edges empty.
                </p>
            </div>
        </section>
    </div>
</template>
