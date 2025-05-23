<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="className" v-bind="$attrs">
        <path
            fill="currentColor"
            d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H3V8h18v8zM6 15h2v-2h2v-2H8V9H6v2H4v2h2z"
        />
        <path
            fill="currentColor"
            d="M16 15c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"
        />
        <path
            fill="currentColor"
            d="M18 9c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1z"
        />
    </svg>
</template>
