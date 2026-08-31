<script setup lang="ts">
import { Check, Search } from '@lucide/vue';
import { computed, ref, useId } from 'vue';

const props = defineProps<{
    markets: string[];
    fieldClasses: string;
}>();

const countryListId = useId();
const isOpen = ref(false);
const query = ref('');
const activeIndex = ref(-1);

const filteredMarkets = computed(() => {
    const normalizedQuery = query.value.trim().toLocaleLowerCase();

    return props.markets
        .filter((market) =>
            market.toLocaleLowerCase().includes(normalizedQuery),
        )
        .slice(0, 12);
});

function showOptions(): void {
    isOpen.value = true;
    activeIndex.value = -1;
}

function hideOptions(): void {
    setTimeout(() => {
        isOpen.value = false;
        activeIndex.value = -1;
    }, 120);
}

function selectMarket(market: string): void {
    query.value = market;
    isOpen.value = false;
    activeIndex.value = -1;
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        isOpen.value = false;
        activeIndex.value = -1;

        return;
    }

    if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) {
        return;
    }

    if (!isOpen.value) {
        showOptions();
    }

    if (filteredMarkets.value.length === 0) {
        return;
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex.value =
            (activeIndex.value + 1) % filteredMarkets.value.length;

        return;
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex.value =
            (activeIndex.value - 1 + filteredMarkets.value.length) %
            filteredMarkets.value.length;

        return;
    }

    if (activeIndex.value >= 0) {
        event.preventDefault();
        selectMarket(filteredMarkets.value[activeIndex.value]);
    }
}
</script>

<template>
    <div class="relative">
        <Search
            class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-[#9aa5c4]"
        />
        <input
            id="country"
            v-model="query"
            name="country"
            type="text"
            required
            autocomplete="country-name"
            placeholder="Country / Market"
            role="combobox"
            aria-autocomplete="list"
            :aria-controls="countryListId"
            :aria-expanded="isOpen"
            :aria-activedescendant="
                activeIndex >= 0
                    ? `${countryListId}-option-${activeIndex}`
                    : undefined
            "
            :class="[fieldClasses, 'pl-11']"
            @focus="showOptions"
            @blur="hideOptions"
            @input="showOptions"
            @keydown="handleKeydown"
        />

        <div
            v-if="isOpen"
            :id="countryListId"
            role="listbox"
            class="absolute top-full z-30 mt-2 max-h-52 w-full overflow-y-auto border border-white/15 bg-[#14264f] p-1 shadow-[0_18px_42px_-20px_rgba(0,0,0,0.8)]"
        >
            <button
                v-for="(market, index) in filteredMarkets"
                :id="`${countryListId}-option-${index}`"
                :key="market"
                type="button"
                role="option"
                :aria-selected="query === market"
                :class="[
                    'flex w-full items-center justify-between px-3 py-2.5 text-left text-sm text-white transition',
                    activeIndex === index ? 'bg-white/15' : 'hover:bg-white/10',
                ]"
                @mousedown.prevent="selectMarket(market)"
            >
                {{ market }}
                <Check v-if="query === market" class="size-4 text-[#d4af37]" />
            </button>

            <p
                v-if="filteredMarkets.length === 0"
                class="px-3 py-2.5 text-sm text-[#9aa5c4]"
            >
                No matching country found.
            </p>
        </div>
    </div>
</template>
