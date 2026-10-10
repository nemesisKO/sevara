<script setup lang="ts">
import { Globe, MessageCircle, Music2 } from '@lucide/vue';
import type { SocialIconName } from '@/types/setting';

defineProps<{ icon: SocialIconName }>();
</script>

<template>
    <Globe v-if="icon === 'website'" class="size-4" aria-hidden="true" />
    <MessageCircle
        v-else-if="icon === 'whatsapp'"
        class="size-4"
        aria-hidden="true"
    />
    <Music2 v-else-if="icon === 'tiktok'" class="size-4" aria-hidden="true" />
    <svg
        v-else
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="size-4"
        aria-hidden="true"
    >
        <template v-if="icon === 'instagram'">
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle
                cx="17.2"
                cy="6.8"
                r="0.6"
                fill="currentColor"
                stroke="none"
            />
        </template>
        <template v-else-if="icon === 'linkedin'">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <line x1="7" y1="10.5" x2="7" y2="17" />
            <circle cx="7" cy="6.8" r="0.6" fill="currentColor" stroke="none" />
            <path d="M11 17v-4.2a2.3 2.3 0 0 1 4.6 0V17" />
            <line x1="11" y1="10.5" x2="11" y2="17" />
        </template>
        <template v-else-if="icon === 'facebook'">
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <path
                d="M14 20v-6h2l.3-3H14V9.3A1.3 1.3 0 0 1 15.3 8H16V5.3A16 16 0 0 0 14.2 5 3.2 3.2 0 0 0 11 8.4V11H9v3h2v6"
            />
        </template>
        <template v-else-if="icon === 'youtube'">
            <rect x="2" y="5" width="20" height="14" rx="4" />
            <path d="m10 9 5 3-5 3z" fill="currentColor" stroke="none" />
        </template>
        <template v-else-if="icon === 'x'">
            <path d="M4 4h3.5L20 20h-3.5z" />
            <path d="M20 4 4 20" />
        </template>
    </svg>
</template>
