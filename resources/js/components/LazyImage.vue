<script setup lang="ts">
import { computed, onMounted, ref, useAttrs, useTemplateRef, watch } from 'vue';

const { src, loading = 'lazy' } = defineProps<{
    src?: string | null;
    loading?: 'lazy' | 'eager';
}>();

defineOptions({ inheritAttrs: false });

const attrs = useAttrs();
const image = useTemplateRef<HTMLImageElement>('image');
const isLoaded = ref(false);

/**
 * Every attribute except the class, which is merged with the skeleton
 * placeholder separately.
 */
const passthrough = computed(() => {
    const rest = { ...attrs };
    delete rest.class;

    return rest;
});

function markAsLoaded(): void {
    isLoaded.value = true;
}

/**
 * A cached image can finish decoding before Vue attaches the load listener,
 * which would leave the skeleton on screen forever.
 */
onMounted(() => {
    if (image.value?.complete && image.value.naturalWidth > 0) {
        isLoaded.value = true;
    }
});

watch(
    () => src,
    () => {
        isLoaded.value = false;
    },
);
</script>

<template>
    <img
        ref="image"
        v-bind="passthrough"
        :src="src ?? undefined"
        :loading="loading"
        decoding="async"
        :class="[attrs.class, { 'image-skeleton': !isLoaded }]"
        @load="markAsLoaded"
        @error="markAsLoaded"
    />
</template>
