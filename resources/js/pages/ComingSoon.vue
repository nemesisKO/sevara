<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Head :title="title" />

    <div
        class="mx-auto flex min-h-[60vh] max-w-2xl flex-col items-center justify-center px-6 py-24 text-center"
    >
        <h1 class="font-serif text-3xl text-[#0f1c3f]">{{ title }}</h1>
        <p class="mt-4 text-[#3a3a35]">
            We're working on this page. Check back soon.
        </p>
    </div>
</template>
