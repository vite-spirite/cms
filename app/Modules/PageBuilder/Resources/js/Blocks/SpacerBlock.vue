<template>
    <div :class="classes" :style="styles" />
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';

const { height } = defineProps<{ height: number }>();

const classes = twMerge('block w-full');
const styles = computed(() => ({
    height: `${height * 4}px`,
}));
</script>
