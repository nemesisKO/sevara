<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import LazyImage from '@/components/LazyImage.vue';
import { home } from '@/routes';

const page = usePage();
const logoUrl = computed(() => page.props.settings.logo_url);

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <LazyImage
                            v-if="logoUrl"
                            :src="logoUrl"
                            alt="Sevara"
                            class="h-10 max-w-48 object-contain"
                        />
                        <span
                            v-else
                            class="font-serif text-3xl tracking-[0.3em] text-[#0f1c3f] dark:text-white"
                        >
                            SEVARA
                        </span>
                        <span class="sr-only">Sevara</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
