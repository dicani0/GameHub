<script setup lang="ts">
import { computed } from 'vue';
import { 
    Target, 
    Skull, 
    Users, 
    Crosshair, 
    Zap, 
    BarChart3,
    Trophy,
    Shield,
    Eye,
    Flame,
    Award,
    Activity,
    Timer
} from 'lucide-vue-next';

interface Props {
    stats: any;
}

const props = defineProps<Props>();

const winRatioTextClass = computed(() => {
    const winRate = props.stats['win_rate%'];
    if (winRate > 50) return 'text-green-600';
    if (winRate < 50) return 'text-red-600';
    return 'text-yellow-400';
});

const winRatioBgClass = computed(() => {
    const winRate = props.stats['win_rate%'];
    if (winRate > 50) return 'bg-green-600';
    if (winRate < 50) return 'bg-red-600';
    return 'bg-yellow-400';
});
</script>

<template>
    <div class="mt-4">
        <div class="border-primary-900 bg-primary-100/20 rounded-lg border border-4 p-6">
            <div class="mb-6 flex items-center gap-3">
                <BarChart3 class="h-6 w-6 text-green-600 dark:text-green-400" />
                <h3 class="text-xl font-semibold text-green-700 dark:text-green-400">Comprehensive Player Statistics</h3>
            </div>

            <div v-if="stats" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-gradient-to-br from-green-50 to-green-100 p-4 dark:from-green-900/20 dark:to-green-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-green-500 p-2">
                                <Trophy class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-green-700 dark:text-green-300">Win Performance</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="text-center mb-3">
                                <div :class="winRatioTextClass" class="text-3xl font-bold">{{ stats['win_rate%'] }}%</div>
                                <div class="mt-2">
                                    <div class="h-2 bg-green-200 rounded-full dark:bg-slate-800">
                                        <div
                                            :class="winRatioBgClass"
                                            class="h-2 rounded-full transition-all duration-500"
                                            :style="{ width: `${stats['win_rate%']}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600 dark:text-green-400">Wins</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ stats.wins }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600 dark:text-green-400">Current Streak</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ stats.current_win_streak }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600 dark:text-green-400">Longest Streak</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ stats.longest_win_streak }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-red-50 to-red-100 p-4 dark:from-red-900/20 dark:to-red-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-red-500 p-2">
                                <Target class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-red-700 dark:text-red-300">Combat Stats</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-red-600 dark:text-red-400">Total Kills</span>
                                <span class="font-bold text-red-800 dark:text-red-200">{{ stats.total_kills_with_extended_stats }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-600 dark:text-red-400">ADR</span>
                                <span class="font-bold text-red-800 dark:text-red-200">{{ stats.adr }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-600 dark:text-red-400">Total Damage</span>
                                <span class="font-bold text-red-800 dark:text-red-200">{{ stats.total_damage.toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 p-4 dark:from-orange-900/20 dark:to-orange-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-orange-500 p-2">
                                <Crosshair class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-orange-700 dark:text-orange-300">Accuracy</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="text-center mb-3">
                                <div class="text-2xl font-bold text-orange-800 dark:text-orange-200">{{ stats['average_headshots%'] }}%</div>
                                <div class="text-xs text-orange-600 dark:text-orange-400">Avg Headshot %</div>
                                <div class="mt-2">
                                    <div class="h-2 bg-orange-200 rounded-full dark:bg-orange-800">
                                        <div 
                                            class="h-2 bg-orange-500 rounded-full transition-all duration-500"
                                            :style="{ width: `${stats['average_headshots%']}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 p-4 dark:from-purple-900/20 dark:to-purple-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-purple-500 p-2">
                                <Shield class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-purple-700 dark:text-purple-300">Clutch Stats</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-600 dark:text-purple-400">1v1 Win Rate</span>
                                <span class="font-bold text-purple-800 dark:text-purple-200">{{ Number(stats['1v1_win_rate'] * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-600 dark:text-purple-400">1v2 Win Rate</span>
                                <span class="font-bold text-purple-800 dark:text-purple-200">{{ Number(stats['1v2_win_rate'] * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-600 dark:text-purple-400">Total 1v2s</span>
                                <span class="font-bold text-purple-800 dark:text-purple-200">{{ stats.total1v2_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 p-4 dark:from-blue-900/20 dark:to-blue-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-blue-500 p-2">
                                <Flame class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-blue-700 dark:text-blue-300">Utility Performance</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-600 dark:text-blue-400">Flash Success Rate</span>
                                <span class="font-bold text-blue-800 dark:text-blue-200">{{ Number(stats.flash_success_rate * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-600 dark:text-blue-400">Utility Success Rate</span>
                                <span class="font-bold text-blue-800 dark:text-blue-200">{{ Number(stats.utility_success_rate * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-600 dark:text-blue-400">Utility/Round</span>
                                <span class="font-bold text-blue-800 dark:text-blue-200">{{ Number(stats.utility_usage_per_round).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-600 dark:text-blue-400">Enemies Flashed/Round</span>
                                <span class="font-bold text-blue-800 dark:text-blue-200">{{ Number(stats.enemies_flashed_per_round).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 dark:from-indigo-900/20 dark:to-indigo-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-indigo-500 p-2">
                                <Activity class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-indigo-700 dark:text-indigo-300">Entry Stats</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-indigo-600 dark:text-indigo-400">Entry Rate</span>
                                <span class="font-bold text-indigo-800 dark:text-indigo-200">{{ Number(stats.entry_rate * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-indigo-600 dark:text-indigo-400">Total Entries</span>
                                <span class="font-bold text-indigo-800 dark:text-indigo-200">{{ stats.total_entry_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-indigo-600 dark:text-indigo-400">Total Matches</span>
                                <span class="font-bold text-indigo-800 dark:text-indigo-200">{{ stats.total_matches }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 dark:from-yellow-900/20 dark:to-yellow-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-yellow-500 p-2">
                                <Eye class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-yellow-700 dark:text-yellow-300">Sniper Stats</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-yellow-600 dark:text-yellow-400">Sniper Kills</span>
                                <span class="font-bold text-yellow-800 dark:text-yellow-200">{{ stats.total_sniper_kills }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-yellow-600 dark:text-yellow-400">Sniper Kill Rate</span>
                                <span class="font-bold text-yellow-800 dark:text-yellow-200">{{ Number(stats.sniper_kill_rate * 100).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-yellow-600 dark:text-yellow-400">Sniper/Round</span>
                                <span class="font-bold text-yellow-800 dark:text-yellow-200">{{ Number(stats.sniper_kill_rate_per_round).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl bg-gradient-to-br from-teal-50 to-teal-100 p-4 dark:from-teal-900/20 dark:to-teal-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-teal-500 p-2">
                                <Zap class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-teal-700 dark:text-teal-300">Utility Damage</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-teal-600 dark:text-teal-400">Total Utility Damage</span>
                                <span class="font-bold text-teal-800 dark:text-teal-200">{{ Number(stats.total_utility_damage).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-teal-600 dark:text-teal-400">Utility Damage/Round</span>
                                <span class="font-bold text-teal-800 dark:text-teal-200">{{ Number(stats.utility_damage_per_round).toFixed(1) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-teal-600 dark:text-teal-400">Success Rate</span>
                                <span class="font-bold text-teal-800 dark:text-teal-200">{{ Number(stats.utility_damage_success_rate).toFixed(1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 p-4 dark:from-amber-900/20 dark:to-amber-800/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="rounded-lg bg-amber-500 p-2">
                                <Award class="h-5 w-5 text-white" />
                            </div>
                            <h4 class="font-semibold text-amber-700 dark:text-amber-300">Flash Performance</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-amber-600 dark:text-amber-400">Flash Successes</span>
                                <span class="font-bold text-amber-800 dark:text-amber-200">{{ stats.total_flash_successes }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-amber-600 dark:text-amber-400">Enemies Flashed</span>
                                <span class="font-bold text-amber-800 dark:text-amber-200">{{ stats.total_enemies_flashed }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-amber-600 dark:text-amber-400">Total Utility Count</span>
                                <span class="font-bold text-amber-800 dark:text-amber-200">{{ stats.total_utility_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="stats.recent_results && stats.recent_results.length > 0" class="rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 dark:from-slate-900/20 dark:to-slate-800/20">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="rounded-lg bg-slate-500 p-2">
                            <Timer class="h-5 w-5 text-white" />
                        </div>
                        <h4 class="font-semibold text-slate-700 dark:text-slate-300">Recent Match Results</h4>
                    </div>
                    <div class="flex gap-2 justify-center">
                        <div 
                            v-for="(result, index) in stats.recent_results" 
                            :key="index"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                            :class="result === '1' ? 'bg-green-500' : 'bg-red-500'"
                        >
                            {{ result === '1' ? 'W' : 'L' }}
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-neutral-50 p-4 text-center dark:bg-neutral-800/50">
                    <div class="flex items-center justify-center gap-2">
                        <Users class="h-4 w-4 text-neutral-600 dark:text-neutral-400" />
                        <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">
                            Statistics from {{ stats.total_matches }} total matches
                        </span>
                    </div>
                </div>
            </div>

            <div v-else class="py-8 text-center">
                <Skull class="mx-auto h-12 w-12 text-neutral-400 dark:text-neutral-600 mb-3" />
                <span class="text-sm text-neutral-500 dark:text-neutral-400">No stats available</span>
            </div>
        </div>
    </div>
</template>