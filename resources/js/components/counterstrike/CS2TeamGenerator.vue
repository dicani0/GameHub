<script setup lang="ts">
import { ref, computed } from 'vue';

const playerNames = ref<string[]>(['', '', '', '', '', '', '', '', '', '']);
const isAnimating = ref(false);
const teamA = ref<string[]>([]);
const teamB = ref<string[]>([]);
const animationStep = ref(0);

const validPlayerNames = computed(() => {
    return playerNames.value.filter(name => name.trim() !== '');
});

const hasEnoughPlayers = computed(() => {
    return validPlayerNames.value.length >= 2;
});

const updatePlayerName = (index: number, name: string) => {
    playerNames.value[index] = name;
};

const shuffleArray = (array: string[]) => {
    const result = [...array];
    for (let i = result.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [result[i], result[j]] = [result[j], result[i]];
    }
    return result;
};

// Function to generate teams
const generateTeams = () => {
    if (!hasEnoughPlayers.value || isAnimating.value) return;
    
    isAnimating.value = true;
    animationStep.value = 0;
    
    // Animation with 3 shuffles
    const animateTeams = () => {
        const shuffled = shuffleArray(validPlayerNames.value);
        const midpoint = Math.ceil(shuffled.length / 2);
        
        teamA.value = shuffled.slice(0, midpoint);
        teamB.value = shuffled.slice(midpoint);
        
        animationStep.value++;
        
        if (animationStep.value < 3) {
            setTimeout(animateTeams, 800);
        } else {
            setTimeout(() => {
                isAnimating.value = false;
            }, 500);
        }
    };
    
    animateTeams();
};
</script>

<template>
    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
        <h2 class="mb-4 text-xl font-semibold">Team Generator</h2>
        
        <!-- Player input section -->
        <div class="grid gap-3">
            <div v-for="(player, index) in playerNames" :key="index" class="flex items-center">
                <span class="mr-2 w-6 text-right text-neutral-500">{{ index + 1 }}</span>
                <input
                    type="text"
                    :value="player"
                    @input="updatePlayerName(index, $event.target.value)"
                    class="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 focus:border-primary-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"
                    :placeholder="`Player ${index + 1}`"
                />
            </div>
        </div>
        
        <div class="mt-4 flex justify-center">
            <button
                @click="generateTeams"
                :disabled="!hasEnoughPlayers || isAnimating"
                :class="[
                    'rounded-md px-4 py-2 font-medium text-white transition-colors',
                    hasEnoughPlayers && !isAnimating
                        ? 'bg-primary-600 hover:bg-primary-900'
                        : 'cursor-not-allowed bg-neutral-400',
                ]"
            >
                {{ isAnimating ? 'Randomizing...' : 'Randomize Teams' }}
            </button>
        </div>
        
        <!-- Teams display section -->
        <div class="mt-4">
            <div v-if="teamA.length === 0 && teamB.length === 0" class="flex h-64 items-center justify-center text-neutral-500">
                <p>Teams will appear here after randomization</p>
            </div>
            
            <div v-else class="grid gap-6 md:grid-cols-2">
                <!-- Team A -->
                <div 
                    class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-900 dark:bg-blue-900/20"
                    :class="{ 'animate-pulse': isAnimating }"
                >
                    <h3 class="mb-2 font-semibold text-blue-700 dark:text-blue-400">Team A</h3>
                    <ul class="space-y-1">
                        <li 
                            v-for="(player, index) in teamA" 
                            :key="index"
                            class="rounded bg-white px-2 py-1 dark:bg-neutral-800"
                        >
                            {{ player }}
                        </li>
                    </ul>
                </div>
                
                <!-- Team B -->
                <div 
                    class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-900 dark:bg-red-900/20"
                    :class="{ 'animate-pulse': isAnimating }"
                >
                    <h3 class="mb-2 font-semibold text-red-700 dark:text-red-400">Team B</h3>
                    <ul class="space-y-1">
                        <li 
                            v-for="(player, index) in teamB" 
                            :key="index"
                            class="rounded bg-white px-2 py-1 dark:bg-neutral-800"
                        >
                            {{ player }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>