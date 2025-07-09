<script setup lang="ts">
import Loader from '@/components/other/Loader.vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    selectedMatch: any;
    matchDetails: any;
    loadingMatchDetails: boolean;
    matchDetailsError: string | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
}>();

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Dialog :open="selectedMatch !== null" @update:open="(open) => { if (!open) closeModal(); }">
        <DialogContent class="max-h-[80vh] overflow-y-auto min-w-7xl">
            <DialogHeader class="text-center">
                <div class="flex items-center justify-between text-center">
                    <DialogTitle class="text-xl font-bold text-center">Match Details</DialogTitle>
                </div>
            </DialogHeader>

            <div v-if="loadingMatchDetails" class="flex justify-center py-8">
                <Loader />
            </div>

            <div v-else-if="matchDetailsError" class="py-4 text-center text-red-600 dark:text-red-400">
                <p>{{ matchDetailsError }}</p>
            </div>

            <div v-else-if="matchDetails" class="space-y-6">
                <div class="border-b border-neutral-200 pb-4 dark:border-neutral-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div class="text-center">
                            <img :src="matchDetails.match.teams.faction1.avatar" class="w-16 h-16 mx-auto rounded-full mb-2" alt="Team 1" />
                            <h3 class="font-bold text-lg">{{ matchDetails.match.teams.faction1.name }}</h3>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold mb-2">VS</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ matchDetails.match.competition_name }}
                            </div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ new Date(matchDetails.match.started_at * 1000).toLocaleDateString() }}
                            </div>
                        </div>
                        <div class="text-center">
                            <img :src="matchDetails.match.teams.faction2.avatar" class="w-16 h-16 mx-auto rounded-full mb-2" alt="Team 2" />
                            <h3 class="font-bold text-lg">{{ matchDetails.match.teams.faction2.name }}</h3>
                        </div>
                    </div>
                </div>

                <div v-if="matchDetails.match.results" class="grid grid-cols-1 gap-6">
                    <div class="text-center">
                        <h4 class="font-bold text-lg mb-3">Map & Results</h4>
                        <div class="space-y-2">
                            <div><strong>Map:</strong> {{ matchDetails.match.voting?.map?.pick?.[0] || 'Unknown' }}</div>
                            <div><strong>Winner:</strong> {{ matchDetails.match.results.winner === 'faction1' ? matchDetails.match.teams.faction1.name : matchDetails.match.teams.faction2.name }}</div>
                        </div>
                    </div>

                    <div v-if="matchDetails.stats && matchDetails.stats.rounds && matchDetails.stats.rounds.length > 0">
                        <h4 class="font-bold text-lg mb-3 text-center">Players Performance</h4>
                        <div class="space-y-3">
                            <div v-for="round in matchDetails.stats.rounds" :key="round.match_id" class="border border-neutral-200 rounded-lg p-3 dark:border-neutral-700">
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="team in round.teams" :key="team.team_id" class="space-y-1">
                                        <div class="font-medium text-sm mb-4">{{ team.team_stats.Team || 'Team' }}</div>
                                        <div v-for="player in team.players" :key="player.player_id" class="text-xs space-y-1 border-b-4">
                                            <div class="flex justify-between ">
                                                <span>{{ player.nickname }}</span>
                                                <span>{{ player.player_stats.Kills }}/{{ player.player_stats.Deaths }}/{{ player.player_stats.Assists }}</span>
                                            </div>
                                            <div class="text-neutral-500 dark:text-neutral-400">
                                                K/D: {{ player.player_stats['K/D Ratio'] }} |
                                                HS: {{ player.player_stats['Headshots %'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-4 text-neutral-600 dark:text-neutral-400">
                    <p>Detailed match statistics are not available for this match.</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
