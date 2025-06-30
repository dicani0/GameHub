<script setup lang="ts">
import { ref, computed } from 'vue';

const availableMaps = [
    { id: 'dust2', name: 'Dust2', enabled: true },
    { id: 'mirage', name: 'Mirage', enabled: true },
    { id: 'inferno', name: 'Inferno', enabled: true },
    { id: 'anubis', name: 'Anubis', enabled: true },
    { id: 'ancient', name: 'Ancient', enabled: true },
    { id: 'train', name: 'Train', enabled: true },
    { id: 'nuke', name: 'Nuke', enabled: true },
];

const maps = ref(availableMaps);
const selectedMap = ref('');
const isAnimating = ref(false);
const animationStep = ref(0);

const enabledMaps = computed(() => {
    return maps.value.filter(map => map.enabled);
});

const hasEnabledMaps = computed(() => {
    return enabledMaps.value.length > 0;
});

const toggleMap = (id: string) => {
    const mapIndex = maps.value.findIndex(map => map.id === id);
    if (mapIndex !== -1) {
        maps.value[mapIndex].enabled = !maps.value[mapIndex].enabled;
    }
};

const shuffleArray = <T>(array: T[]): T[] => {
    const result = [...array];
    for (let i = result.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [result[i], result[j]] = [result[j], result[i]];
    }
    return result;
};

const randomizeMap = () => {
    if (!hasEnabledMaps.value || isAnimating.value) return;
    
    isAnimating.value = true;
    animationStep.value = 0;

    const animateMapSelection = () => {
        const shuffled = shuffleArray(enabledMaps.value);
        selectedMap.value = shuffled[0].name;
        
        animationStep.value++;
        
        if (animationStep.value < 3) {
            setTimeout(animateMapSelection, 800);
        } else {
            setTimeout(() => {
                isAnimating.value = false;
            }, 500);
        }
    };
    
    animateMapSelection();
};
</script>

<template>
    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
        <h2 class="mb-4 text-xl font-semibold">Map Randomizer</h2>

        <div class="mb-4">
            <h3 class="mb-2 font-medium">Available Maps</h3>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="map in maps"
                    :key="map.id"
                    @click="toggleMap(map.id)"
                    :class="[
                        'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                        map.enabled
                            ? 'bg-primary-100 text-primary-700 hover:bg-primary-200 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-900/50'
                            : 'bg-red-100 text-neutral-500 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                    ]"
                >
                    {{ map.name }}
                </button>
            </div>
        </div>

        <div class="mb-4 flex justify-center">
            <button
                @click="randomizeMap"
                :disabled="!hasEnabledMaps || isAnimating"
                :class="[
                    'rounded-md px-4 py-2 font-medium text-white transition-colors',
                    hasEnabledMaps && !isAnimating
                        ? 'bg-primary-500 hover:bg-primary-600'
                        : 'cursor-not-allowed bg-neutral-400',
                ]"
            >
                {{ isAnimating ? 'Randomizing...' : 'Randomize Map' }}
            </button>
        </div>

        <div v-if="selectedMap" class="mt-4">
            <h3 class="mb-2 font-medium">Selected Map</h3>
            <div 
                class="flex h-16 items-center justify-center rounded-lg border border-green-200 bg-green-50 p-3 text-xl font-bold text-green-700 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400"
                :class="{ 'animate-pulse': isAnimating }"
            >
                {{ selectedMap }}
            </div>
        </div>
        
        <div v-else class="mt-4 flex h-16 items-center justify-center text-neutral-500">
            <p>Selected map will appear here after randomization</p>
        </div>
    </div>
</template>