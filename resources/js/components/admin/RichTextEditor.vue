<script setup lang="ts">
import {
    Bold,
    Italic,
    Link as LinkIcon,
    List,
    ListOrdered,
    RemoveFormatting,
    Underline,
} from '@lucide/vue';
import { onMounted, ref, useTemplateRef } from 'vue';

const props = defineProps<{
    defaultValue?: string | null;
    name: string;
    placeholder?: string;
}>();

const editor = useTemplateRef('editor');
const content = ref(props.defaultValue ?? '');

const buttonClass =
    'flex size-7 items-center justify-center rounded text-neutral-600 transition-colors hover:bg-neutral-200 hover:text-neutral-900';

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = content.value;
    }
});

function onInput(): void {
    content.value = editor.value?.innerHTML ?? '';
}

function exec(command: string, value?: string): void {
    editor.value?.focus();
    document.execCommand(command, false, value);
    onInput();
}

function toggleLink(): void {
    const selection = window.getSelection();

    if (!selection || selection.isCollapsed) {
        window.alert('Select some text first to turn it into a link.');

        return;
    }

    const url = window.prompt('Link URL (e.g. https://example.com)');

    if (url) {
        exec('createLink', url);
    }
}

function onHeadingChange(event: Event): void {
    const value = (event.target as HTMLSelectElement).value;
    exec('formatBlock', value);
    (event.target as HTMLSelectElement).value = '';
}
</script>

<template>
    <div class="rounded-md border border-neutral-300 bg-white">
        <div
            class="flex flex-wrap items-center gap-1 border-b border-neutral-200 bg-neutral-50 p-1.5"
        >
            <select
                class="h-7 rounded border-0 bg-transparent px-1 text-xs text-neutral-600 outline-none hover:bg-neutral-200"
                @change="onHeadingChange"
            >
                <option value="">Paragraph</option>
                <option value="h2">Heading 2</option>
                <option value="h3">Heading 3</option>
            </select>

            <span class="mx-1 h-5 w-px bg-neutral-200" />

            <button
                type="button"
                :class="buttonClass"
                aria-label="Bold"
                @mousedown.prevent="exec('bold')"
            >
                <Bold class="size-4" />
            </button>
            <button
                type="button"
                :class="buttonClass"
                aria-label="Italic"
                @mousedown.prevent="exec('italic')"
            >
                <Italic class="size-4" />
            </button>
            <button
                type="button"
                :class="buttonClass"
                aria-label="Underline"
                @mousedown.prevent="exec('underline')"
            >
                <Underline class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-neutral-200" />

            <button
                type="button"
                :class="buttonClass"
                aria-label="Bullet list"
                @mousedown.prevent="exec('insertUnorderedList')"
            >
                <List class="size-4" />
            </button>
            <button
                type="button"
                :class="buttonClass"
                aria-label="Numbered list"
                @mousedown.prevent="exec('insertOrderedList')"
            >
                <ListOrdered class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-neutral-200" />

            <button
                type="button"
                :class="buttonClass"
                aria-label="Insert link"
                @mousedown.prevent="toggleLink"
            >
                <LinkIcon class="size-4" />
            </button>
            <button
                type="button"
                :class="buttonClass"
                aria-label="Clear formatting"
                @mousedown.prevent="exec('removeFormat')"
            >
                <RemoveFormatting class="size-4" />
            </button>
        </div>

        <div
            ref="editor"
            contenteditable="true"
            :data-placeholder="placeholder"
            class="rich-text-editor min-h-40 w-full px-3 py-2.5 text-sm leading-relaxed text-neutral-900 outline-none [&_a]:text-blue-600 [&_a]:underline [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:text-base [&_h3]:font-semibold [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:list-disc [&_ul]:pl-5"
            @input="onInput"
        />

        <input type="hidden" :name="name" :value="content" />
    </div>
</template>

<style scoped>
.rich-text-editor:empty::before {
    content: attr(data-placeholder);
    color: #a3a3a3;
}
</style>
