<template>
    <Head title="FaceIt Search" />
    <div class="border-sidebar-border/70 dark:border-sidebar-border rounded-xl border p-4">
        <div class="mb-4">
            <label for="nickname" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Nickname</label>
            <div class="mt-1 flex rounded-md">
                <input
                    type="text"
                    name="nickname"
                    id="nickname"
                    v-model="nickname"
                    class="focus:border-primary-500 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"
                    placeholder="Enter Faceit nickname"
                />
                <button
                    type="button"
                    @click="searchPlayer"
                    class="ml-3 inline-flex items-center rounded-md px-4 py-2 font-medium text-white transition-colors"
                    :class="[!loading ? 'bg-primary-600 hover:bg-primary-900' : 'cursor-not-allowed bg-neutral-400']"
                    :disabled="loading"
                >
                    <span v-if="loading">Searching...</span>
                    <span v-else>Search</span>
                </button>
            </div>
        </div>

        <div class="mb-4 flex space-x-4">
            <div class="flex items-center">
                <input
                    id="include-stats"
                    type="checkbox"
                    v-model="includeStats"
                    class="text-primary-600 focus:ring-primary-500 h-4 w-4 rounded border-neutral-300 dark:border-neutral-700"
                />
                <label for="include-stats" class="ml-2 block text-sm text-neutral-900 dark:text-neutral-200"> Include Stats </label>
            </div>

            <div class="flex items-center">
                <input
                    id="include-history"
                    type="checkbox"
                    v-model="includeHistory"
                    class="text-primary-600 focus:ring-primary-500 h-4 w-4 rounded border-neutral-300 dark:border-neutral-700"
                />
                <label for="include-history" class="ml-2 block text-sm text-neutral-900 dark:text-neutral-200"> Include Match History </label>
            </div>
        </div>

        <div
            v-if="error"
            class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400"
            role="alert"
        >
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ error }}</span>
        </div>

        <div v-if="player" class="mt-4">
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-900/20">
                <h3 class="mb-2 text-lg font-medium text-blue-700 dark:text-blue-400">Player Information</h3>

                <div class="space-y-3">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Nickname</div>
                        <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                            {{ player.player.nickname }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Country</div>
                        <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                            {{ player.player.country }}
                        </div>
                    </div>

                    <div v-if="player.player.games.cs2.faceit_elo" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">ELO Rating</div>
                        <div class="rounded bg-white px-3 py-2 text-sm text-neutral-900 sm:col-span-2 dark:bg-neutral-800 dark:text-neutral-200">
                            {{ player.player.games.cs2.faceit_elo }}
                        </div>
                    </div>

                    <div v-if="player.player.games.cs2.skill_level" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Skill Level</div>
                        <div class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-sm font-semibold text-neutral-900 w-6"
                             :class="faceitLevelClass">
                            {{ player.player.games.cs2.skill_level }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="player && player.history" class="mt-6">
            <div
                class="rounded-lg p-4">
                <h3 class="mb-4 text-lg font-medium text-red-700 dark:text-red-400">Recent Matches</h3>

                <ul class="space-y-2">
                    <li
                        v-for="match in player.history"
                        :key="match.match_id"
                        class="rounded bg-white p-3 dark:bg-neutral-800"
                        :class="match.results.victory ? 'outline outline-2 outline-green-500 outline-offset-[-2px]' : 'outline outline-2 outline-red-500 outline-offset-[-2px]'"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-primary-600 dark:text-primary-400 truncate text-sm font-medium">
                                {{ match.competition_name }}
                            </p>
                            <div class="ml-2 flex-shrink-0">
                                <p
                                    class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                                    :class="
                                        match.results.victory
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                    "
                                >
                                    {{ match.results.victory ? 'Win' : 'Loss' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <div class="flex items-center text-sm text-neutral-500 dark:text-neutral-400">
                                    <img class="rounded-4xl" :src="match.teams.faction1.avatar" width="50" height="50" alt="" />
                                    <p class="w-75 p-4 text-center">
                                        <span class="font-bold text-base">
                                            {{ match.teams.faction1.nickname }}
                                        </span>
                                        vs
                                        <span class="font-bold text-base">
                                            {{ match.teams.faction2.nickname }}
                                        </span>
                                    </p>
                                    <img class="rounded-4xl" :src="match.teams.faction2.avatar" width="50" height="50" alt="" />
                                </div>
                            </div>
                            <div>
                                <img width="175" :src="`/images/maps/${match.match_details.voting.map.pick[0]}.png`" alt="">
                            </div>
                            <div class="mt-2 flex items-center text-sm text-neutral-500 sm:mt-0 dark:text-neutral-400">
                                <p>
                                    {{ new Date(match.finished_at * 1000).toLocaleDateString('pl-PL') }}
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const nickname = ref('');
const includeStats = ref(true);
const includeHistory = ref(false);
const player = ref(null);
const loading = ref(false);
const error = ref(null);

const faceitLevelClass = computed(() => {
    if (!player.value || !player.value.player || !player.value.player.games.cs2) {
        return '';
    }

    const level = player.value.player.games.cs2.skill_level;
    if (level < 4) return 'bg-green-500';
    if (level <= 10) return 'bg-yellow-300';
    return 'bg-red-700';
});
const searchPlayer = async () => {
    if (!nickname.value) {
        error.value = 'Please enter a nickname';
        return;
    }

    error.value = null;
    loading.value = true;

    try {
        const response = await axios.get('/cs/faceit/player', {
            params: {
                nickname: nickname.value,
                include_stats: includeStats.value,
                include_history: includeHistory.value,
            },
        });

        player.value = response.data;
    } catch (e) {
        if (e.response && e.response.status === 404) {
            error.value = 'Player not found';
        } else {
            error.value = 'An error occurred while fetching player data';
        }

        player.value = null;
    } finally {
        loading.value = false;
    }
};
</script>
