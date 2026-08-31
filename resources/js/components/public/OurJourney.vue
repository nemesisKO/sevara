<script setup lang="ts">
import { ChevronsRight, Diamond, Globe } from '@lucide/vue';
import RevealOnScroll from '@/components/public/RevealOnScroll.vue';

const mandala = "url('/images/mandala.png') center / contain no-repeat";

const steps = [
    {
        icon: 'laurel',
        title: '40+ Years of Experience',
        description:
            'Deep roots in the food industry with long-standing relationships.',
    },
    {
        icon: 'globe',
        title: 'International Trading Expertise',
        description:
            'Global sourcing, export knowledge and market understanding.',
    },
    {
        icon: 'diamond',
        title: 'Sevara Premium Foods for Modern Markets',
        description: 'Premium date confectionery crafted for the world.',
    },
];
</script>

<template>
    <section class="relative mt-16 overflow-hidden bg-[#0f1c3f] py-12 sm:py-16">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <!-- Large anchor mandalas, asymmetric on each side -->
            <div
                class="absolute top-8 -left-56 size-[38rem] -rotate-12 opacity-[0.07]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -right-44 -bottom-40 size-[30rem] rotate-[18deg] opacity-[0.06]"
                :style="{ background: mandala }"
            />
            <!-- Smaller accents filling the emptier gaps toward the center -->
            <div
                class="absolute -top-32 right-1/4 size-72 rotate-6 opacity-[0.05]"
                :style="{ background: mandala }"
            />
            <div
                class="absolute -bottom-24 left-1/4 hidden size-64 -rotate-[24deg] opacity-[0.04] lg:block"
                :style="{ background: mandala }"
            />
        </div>

        <div class="relative mx-auto max-w-6xl px-6 text-center lg:px-10">
            <RevealOnScroll>
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-[#c9a961] sm:text-sm"
                >
                    OUR JOURNEY
                </p>
                <h2
                    class="mx-auto mt-3 max-w-4xl font-serif text-2xl leading-snug text-lightGold sm:mt-4 sm:text-4xl lg:text-5xl"
                >
                    From trading experience to a premium FMCG brand for modern
                    markets.
                </h2>
            </RevealOnScroll>

            <div
                class="mt-10 flex flex-col items-center gap-8 sm:mt-12 sm:grid sm:grid-cols-3 sm:gap-0"
            >
                <RevealOnScroll
                    v-for="(step, index) in steps"
                    :key="step.title"
                    :delay="200 + index * 200"
                    class="relative flex flex-col items-center text-center"
                >
                    <!-- Connector runs from this circle's edge to the next one's -->
                    <div
                        v-if="index < steps.length - 1"
                        class="absolute top-10 right-[calc(-50%+3.25rem)] left-[calc(50%+3.25rem)] hidden -translate-y-1/2 items-center gap-3 text-[#d4af37]/70 sm:flex"
                    >
                        <span class="h-px flex-1 bg-[#d4af37]/30" />
                        <ChevronsRight class="size-5 shrink-0" />
                        <span class="h-px flex-1 bg-[#d4af37]/30" />
                    </div>

                    <div
                        class="flex size-[4.5rem] items-center justify-center rounded-full border-2 border-[#d4af37]/70 text-[#d4af37] shadow-[0_0_0_4px_rgba(212,175,55,0.08)] sm:size-24"
                    >
                        <img
                            v-if="step.icon === 'laurel'"
                            src="/images/journey-laurel-wreath.webp"
                            alt=""
                            class="pointer-events-none size-12 object-contain sm:size-16"
                            aria-hidden="true"
                        />
                        <Globe
                            v-else-if="step.icon === 'globe'"
                            class="size-10 stroke-[1.7] sm:size-12"
                            aria-hidden="true"
                        />
                        <svg
                            v-else
                            viewBox="0 0 64 64"
                            fill="none"
                            class="size-10 sm:size-12"
                            aria-hidden="true"
                        >
                            <rect
                                x="16"
                                y="16"
                                width="32"
                                height="32"
                                transform="rotate(45 32 32)"
                                stroke="currentColor"
                                stroke-width="2.5"
                            />
                            <rect
                                x="22"
                                y="22"
                                width="20"
                                height="20"
                                transform="rotate(45 32 32)"
                                stroke="currentColor"
                                stroke-width="2.5"
                            />
                            <rect
                                x="28"
                                y="28"
                                width="8"
                                height="8"
                                transform="rotate(45 32 32)"
                                stroke="currentColor"
                                stroke-width="2.5"
                            />
                        </svg>
                    </div>
                    <h3
                        class="mt-4 max-w-56 font-serif text-lg leading-snug text-[#eadcbe] sm:mt-5 sm:min-h-14 sm:text-xl"
                    >
                        {{ step.title }}
                    </h3>
                    <p
                        class="mt-2 max-w-64 text-sm leading-relaxed text-[#93a0bd]"
                    >
                        {{ step.description }}
                    </p>
                </RevealOnScroll>
            </div>

            <RevealOnScroll :delay="150">
                <div
                    class="mt-10 flex items-center justify-center gap-4 sm:mt-12"
                >
                    <span class="h-px w-10 bg-[#d4af37]/30 sm:w-16" />
                    <Diamond class="size-3 text-[#d4af37]" />
                    <span class="h-px w-10 bg-[#d4af37]/30 sm:w-16" />
                </div>

                <h3
                    class="mt-5 font-serif text-2xl text-lightGold sm:mt-6 sm:text-3xl lg:text-4xl"
                >
                    From Experience Comes Confidence
                </h3>
                <p
                    class="mx-auto mt-3 max-w-3xl text-sm leading-relaxed text-[#a8b2cb] sm:mt-4 sm:text-base"
                >
                    Sevara is not the beginning of our story, it is the new
                    chapter of a journey shaped by decades of experience,
                    trusted partnerships and a vision for the future.
                </p>
            </RevealOnScroll>
        </div>
    </section>
</template>
