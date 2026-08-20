<script setup lang="ts">
import { Check, Sparkles, X } from '@lucide/vue';

const props = defineProps<{
    message: string;
    onCloseToast?: () => void;
}>();

function closeToast(): void {
    props.onCloseToast?.();
}
</script>

<template>
    <div
        data-test="newsletter-success-toast"
        class="relative w-[min(420px,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-[#d4af37]/35 bg-[#fffdf8] px-5 py-4 text-[#0f1c3f] shadow-[0_20px_60px_rgb(15_28_63/0.2)]"
    >
        <div
            class="pointer-events-none absolute -top-16 -right-12 size-40 rounded-full bg-[#d4af37]/20 blur-3xl"
        />
        <div
            class="pointer-events-none absolute -bottom-20 -left-16 size-40 rounded-full bg-[#0f1c3f]/10 blur-3xl"
        />

        <div class="relative flex items-start gap-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-[#0f1c3f] text-[#d4af37] shadow-lg shadow-[#0f1c3f]/15"
            >
                <Check class="size-5" :stroke-width="2.5" />
            </div>

            <div class="min-w-0 flex-1 pt-0.5">
                <p
                    class="text-[10px] font-semibold tracking-[0.3em] text-[#b48927] uppercase"
                >
                    Sevara
                </p>
                <p class="mt-1 text-lg font-semibold tracking-tight">
                    You’re on the list
                </p>
                <p class="mt-1 text-sm leading-5 text-[#59627a]">
                    {{ message }}
                </p>
            </div>

            <button
                type="button"
                aria-label="Close notification"
                class="flex size-7 shrink-0 items-center justify-center rounded-full text-[#7e8495] transition hover:bg-[#0f1c3f]/5 hover:text-[#0f1c3f]"
                @click="closeToast"
            >
                <X class="size-4" />
            </button>
        </div>

        <div
            class="relative mt-4 flex items-center gap-2 border-t border-[#0f1c3f]/10 pt-3 text-xs font-medium tracking-wide text-[#7e8495]"
        >
            <Sparkles class="size-3.5 text-[#d4af37]" />
            Exclusive updates are coming your way.
        </div>
    </div>
</template>
