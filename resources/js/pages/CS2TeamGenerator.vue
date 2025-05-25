<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CS2TeamGenerator from '@/components/CS2TeamGenerator.vue';
import CS2MapRandomizer from '@/components/CS2MapRandomizer.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'CS2 Team Generator',
        href: '/cs/team-generator',
    },
];
</script>

<template>
    <Head title="CS2 Team Generator" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">CS2 Team Generator</h1>
                <p class="text-neutral-500 dark:text-neutral-400">
                    Generate balanced teams and pick a random map for your CS2 matches.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <CS2TeamGenerator />

                <CS2MapRandomizer />
            </div>
        </div>
    </AppLayout>
</template>
