<script setup lang="ts">
import { Diamond } from '@lucide/vue';
import { computed } from 'vue';

const { label, tone = 'light' } = defineProps<{
    label?: string;
    tone?: 'light' | 'dark';
}>();

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const toneClasses = computed(() =>
    tone === 'dark'
        ? {
              surface: 'bg-white/[0.03]',
              wordmark: 'text-white/30',
              rule: 'bg-white/20',
              accent: 'text-[#d4af37]/45',
              label: 'text-[#d4af37]/50',
          }
        : {
              surface: 'bg-[#f7f3e8]',
              wordmark: 'text-[#0f1c3f]/20',
              rule: 'bg-[#0f1c3f]/15',
              accent: 'text-[#0f1c3f]/25',
              label: 'text-[#0f1c3f]/30',
          },
);
</script>

<template>
    <div
        class="relative flex h-full w-full items-center justify-center overflow-hidden"
        :class="toneClasses.surface"
    >
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.1] grayscale"
            :style="{ background: mandala }"
        />

        <div class="relative flex flex-col items-center gap-4 px-4 text-center">
            <p
                class="font-serif text-2xl tracking-[0.35em] sm:text-3xl"
                :class="toneClasses.wordmark"
            >
                SEVARA
            </p>
            <div class="flex items-center gap-3">
                <span class="h-px w-8" :class="toneClasses.rule" />
                <Diamond class="size-2.5" :class="toneClasses.accent" />
                <span class="h-px w-8" :class="toneClasses.rule" />
            </div>
            <p
                v-if="label"
                class="text-[10px] font-semibold tracking-[0.3em] uppercase"
                :class="toneClasses.label"
            >
                {{ label }}
            </p>
        </div>
    </div>
</template>
