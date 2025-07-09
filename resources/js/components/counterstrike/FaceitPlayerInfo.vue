<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    player: any;
}

const props = defineProps<Props>();

const faceitLevelClass = computed(() => {
    if (!props.player || !props.player.games?.cs2) {
        return '';
    }

    const level = props.player.games.cs2.skill_level;
    if (level < 4) return 'bg-green-500 ring-green-500';
    if (level < 10) return 'bg-yellow-300 ring-yellow-300';
    return 'bg-red-700 ring-red-500';
});
</script>

<template>
    <div class="mt-4">
        <div class="border-primary-900 bg-primary-100/20 rounded-lg border border-4 p-4">
            <h3 class="mb-2 text-lg font-medium text-blue-700 dark:text-blue-400">Player Information</h3>

            <div class="space-y-3">
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Nickname</div>
                    <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                        {{ player.nickname }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Country</div>
                    <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                        {{ player.country }}
                    </div>
                </div>

                <div v-if="player.games?.cs2?.faceit_elo" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">ELO Rating</div>
                    <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                        {{ player.games.cs2.faceit_elo }}
                    </div>
                </div>

                <div v-if="player.games?.cs2?.skill_level" class="grid grid-cols-1 gap-2 p-2 sm:grid-cols-3">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Skill Level</div>
                    <div
                        class="inline-flex w-6 items-center justify-center rounded-full px-2 py-0.5 text-sm font-semibold text-neutral-900 ring ring-offset-1 ring-offset-black"
                        :class="faceitLevelClass"
                    >
                        {{ player.games.cs2.skill_level }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>