<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Diamond } from '@lucide/vue';
import { privateLabel } from '@/routes';
</script>

<template>
    <section aria-label="Explore private label">
        <Link
            :href="privateLabel()"
            class="group relative block min-h-60 overflow-hidden bg-[#061721] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d4af37] lg:aspect-[20/3] lg:min-h-0"
        >
            <img
                src="/images/private-banner-final.webp"
                alt=""
                class="absolute inset-0 h-full w-full object-cover object-[70%_center] lg:object-center"
                loading="lazy"
                decoding="async"
            />
            <span
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-r from-[#061721] via-[#061721]/85 to-transparent lg:via-[#061721]/35"
            />
            <div
                class="relative flex min-h-60 w-[72%] flex-col justify-center py-7 pl-[7%] text-[#e7bd76] sm:w-[60%] sm:pl-[12%] lg:absolute lg:inset-y-0 lg:left-[15%] lg:min-h-0 lg:w-[38%] lg:p-0"
            >
                <h2
                    class="font-serif text-[clamp(1.5rem,2.2vw,2.5rem)] leading-tight"
                >
                    Intense chocolate date
                </h2>
                <p
                    class="mt-1 text-sm tracking-[0.16em] text-[#d7c7a6] sm:text-base"
                >
                    for modern retail
                </p>
                <div
                    aria-hidden="true"
                    class="mt-3 flex items-center gap-2 text-[#b99a45]"
                >
                    <span class="h-px w-10 bg-current/60" />
                    <Diamond class="size-2.5" />
                    <span class="h-px w-10 bg-current/60" />
                </div>
                <span
                    class="mt-4 inline-flex self-start rounded-sm bg-[#e7bd76] px-4 py-2.5 text-[0.65rem] font-semibold tracking-[0.12em] text-[#0f1c3f] uppercase shadow-sm transition-colors group-hover:bg-[#f7d295] sm:text-xs"
                >
                    Explore Private Label
                </span>
            </div>
        </Link>
    </section>
</template>
