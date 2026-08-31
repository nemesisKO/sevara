<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { privateLabel } from '@/routes';
</script>

<template>
    <section
        class="relative h-40 overflow-hidden bg-[#081019] sm:h-48 lg:h-[205px]"
        style="
            background-image:
                linear-gradient(
                    to right,
                    rgba(8, 16, 25, 0.92) 0%,
                    rgba(8, 16, 25, 0.78) 36%,
                    rgba(8, 16, 25, 0.34) 62%,
                    rgba(25, 29, 35, 0.18) 100%
                ),
                url('/images/private-label-banner-blended.png'),
                linear-gradient(to right, #081019 0%, #081019 52%, #191d23 100%);
            background-position: center, center, center;
            background-repeat: no-repeat, no-repeat, no-repeat;
            background-size:
                100% 100%,
                auto 100%,
                100% 100%;
        "
    >
        <div
            class="pointer-events-none absolute top-0 bottom-0 left-0 w-24 bg-[url('/images/pattern-ornament.webp')] bg-cover bg-left opacity-20 sm:w-28 lg:w-32"
        />

        <div
            class="absolute top-[19%] left-[12%] flex max-w-[72%] flex-col items-start sm:max-w-[44%]"
        >
            <h2
                class="font-serif text-lg leading-[1.35] text-[#ddcfb0] sm:text-2xl lg:text-3xl xl:text-[1.75rem]"
            >
                Intense dark chocolate<br class="hidden xl:block" />
                crafted for modern retail.
            </h2>
            <div class="mt-2 flex items-center gap-2 sm:mt-3">
                <span class="h-px w-8 bg-[#d4af37]/55 sm:w-12" />
                <span class="size-1.5 rotate-45 bg-[#d4af37]" />
                <span class="h-px w-8 bg-[#d4af37]/55 sm:w-12" />
            </div>

            <Link
                :href="privateLabel()"
                class="mt-3 inline-flex items-center bg-[#e7bd76] px-3 py-1.5 text-[0.6rem] font-semibold tracking-[0.13em] text-[#0f1c3f] uppercase transition hover:bg-[#f7d295] sm:px-4 sm:py-2 sm:text-xs"
            >
                Explore Private Label
            </Link>
        </div>
    </section>
</template>
