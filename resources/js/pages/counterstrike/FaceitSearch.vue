<template>
    <AppLayout :breadcrumbs="breadcrumbs">
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-neutral-800">
        <div class="p-6 bg-white border-b border-gray-200 dark:bg-neutral-800 dark:border-neutral-700">
          <h1 class="text-2xl font-semibold mb-6 dark:text-neutral-200">Faceit Player Search</h1>
          <p class="mb-6 text-gray-600 dark:text-neutral-400">
            Search for Faceit players and view their stats and match history.
          </p>

          <FaceitPlayerSearch 
            :player="props.player"
            :stats="props.stats"
            :history="props.history"
            :nickname="props.nickname"
            :includeStats="props.includeStats"
            :includeHistory="props.includeHistory"
            :error="props.error"
          />
        </div>
      </div>
    </div>
  </div>
    </AppLayout>
</template>

<script setup lang="ts">
import FaceitPlayerSearch from '@/components/counterstrike/FaceitPlayerSearch.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { withDefaults, defineProps } from 'vue';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'FaceIT Search',
        href: '/cs/faceit-search',
    },
];
</script>
