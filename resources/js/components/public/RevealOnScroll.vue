<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue';

withDefaults(
    defineProps<{
        delay?: number;
    }>(),
    { delay: 0 },
);

const el = useTemplateRef('el');
const revealed = ref(false);

let observer: IntersectionObserver | null = null;

onMounted(() => {
    if (!el.value) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0]?.isIntersecting) {
                revealed.value = true;
                observer?.disconnect();
            }
        },
        { threshold: 0.15 },
    );

    observer.observe(el.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <div
        ref="el"
        class="transition-all duration-700 ease-out motion-reduce:transition-none"
        :class="
            revealed
                ? 'translate-y-0 scale-100 opacity-100'
                : 'translate-y-10 scale-[0.96] opacity-0 motion-reduce:translate-y-0 motion-reduce:scale-100 motion-reduce:opacity-100'
        "
        :style="{ transitionDelay: `${delay}ms` }"
    >
        <slot />
    </div>
</template>
