<script setup lang="ts">
interface Props {
    history: any[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    matchClicked: [match: any];
}>();

const handleMatchClick = (match: any) => {
    emit('matchClicked', match);
};
</script>

<template>
    <div class="mt-6">
        <div class="border-primary-900 bg-primary-100/20 rounded-lg border border-4 p-4">
            <h3 class="mb-4 text-lg font-medium text-red-700 dark:text-red-400">Recent Matches</h3>

            <div v-if="history && history.length > 0">
                <ul class="space-y-2">
                    <li v-for="match in history" :key="match.match_id" class="bg-primary-950/50 rounded">
                        <div
                            class="rounded-lg p-4 cursor-pointer transition-colors hover:bg-primary-900/30"
                            :class="match.results.victory ? 'border-l-16 border-green-500' : 'border-l-16 border-red-500'"
                            @click="handleMatchClick(match)"
                        >
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <div class="flex items-center text-sm text-neutral-200 dark:text-neutral-400">
                                        <img class="rounded-4xl" :src="match.teams.faction1.avatar" width="50" height="50" alt="" />
                                        <p class="w-75 p-4 text-center">
                                            <span class="text-base font-bold">
                                                {{ match.teams.faction1.nickname }}
                                            </span>
                                            vs
                                            <span class="text-base font-bold">
                                                {{ match.teams.faction2.nickname }}
                                            </span>
                                        </p>
                                        <img class="rounded-4xl" :src="match.teams.faction2.avatar" width="50" height="50" alt="" />
                                    </div>
                                </div>
                                <div>
                                    <img
                                        class="rounded-md"
                                        width="175"
                                        :src="`/images/maps/${match.match_details.voting.map.pick[0]}.png`"
                                        alt=""
                                    />
                                </div>
                                <div class="mt-2 flex items-center text-sm font-bold text-neutral-200 sm:mt-0 dark:text-neutral-400">
                                    <p>
                                        {{ new Date(match.finished_at * 1000).toLocaleDateString('pl-PL') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-else class="py-4 text-center">
                <span class="text-sm text-neutral-500 dark:text-neutral-400">No match history available</span>
            </div>
        </div>
    </div>
</template>