<script setup lang="ts">
import Loader from '@/components/other/Loader.vue';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, defineProps, ref, withDefaults } from 'vue';
import FaceitPlayerInfo from './FaceitPlayerInfo.vue';
import FaceitPlayerStats from './FaceitPlayerStats.vue';
import FaceitMatchHistory from './FaceitMatchHistory.vue';
import FaceitMatchDetailsModal from './FaceitMatchDetailsModal.vue';

interface Props {
    player?: any;
    stats?: any;
    history?: any;
    nickname?: string;
    includeStats?: boolean;
    includeHistory?: boolean;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    player: null,
    stats: null,
    history: null,
    nickname: '',
    includeStats: false,
    includeHistory: false,
    error: null,
});

const nickname = ref(props.nickname || '');
const includeStats = ref(props.includeStats || true);
const includeHistory = ref(props.includeHistory || false);
const player = ref(props.player);
const loading = ref(false);
const error = ref(props.error);

const selectedMatch = ref(null);
const matchDetails = ref(null);
const loadingMatchDetails = ref(false);
const matchDetailsError = ref(null);

const searchPlayer = () => {
    if (!nickname.value) {
        error.value = 'Please enter a nickname';
        return;
    }

    loading.value = true;

    router.visit('/cs/faceit/results', {
        data: {
            nickname: nickname.value,
            include_stats: includeStats.value,
            include_history: includeHistory.value,
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};

const fetchMatchDetails = async (matchId: string) => {
    if (!matchId) return;

    loadingMatchDetails.value = true;
    matchDetailsError.value = null;

    try {
        const response = await fetch(`/cs/faceit/match/${matchId}?include_stats=true`);
        if (!response.ok) {
            throw new Error('Failed to fetch match details');
        }

        const data = await response.json();
        console.log(data);
        matchDetails.value = data;
    } catch (err) {
        matchDetailsError.value = err.message || 'Failed to fetch match details';
        console.error('Error fetching match details:', err);
    } finally {
        loadingMatchDetails.value = false;
    }
};

const openMatchDetails = (match: any) => {
    selectedMatch.value = match;
    fetchMatchDetails(match.match_id);
};

const closeMatchDetails = () => {
    selectedMatch.value = null;
    matchDetails.value = null;
    matchDetailsError.value = null;
};
</script>

<template>
    <Head title="FaceIt Search" />
    <div class="p-4">
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
        <FaceitPlayerInfo v-if="player" :player="player" />
        <Deferred v-if="includeStats && player" data="stats">
            <template #fallback>
                <Loader />
            </template>
            <FaceitPlayerStats :stats="stats" />
        </Deferred>

        <Deferred v-if="includeHistory && player" data="history">
            <template #fallback>
                <Loader />
            </template>
            <FaceitMatchHistory :history="history" @match-clicked="openMatchDetails" />
        </Deferred>

        <FaceitMatchDetailsModal
            :selected-match="selectedMatch"
            :match-details="matchDetails"
            :loading-match-details="loadingMatchDetails"
            :match-details-error="matchDetailsError"
            @close="closeMatchDetails"
        />
    </div>
</template>
