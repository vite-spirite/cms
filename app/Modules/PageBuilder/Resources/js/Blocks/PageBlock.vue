<template>
    <div>
        <slot :container-class="classes" :container-style="styles" />
    </div>
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';
import type { PageBlock } from '../types';

const { color, text_color, spacing_x, spacing_y } = defineProps<{
    id: string;
    selected: boolean;
    editable: boolean;
    color: string;
    text_color: string;
    spacing_x: number;
    spacing_y: number;
    children: PageBlock[];
}>();

const classes = twMerge('min-h-screen w-full');
const styles = computed(() => ({
    padding: `${spacing_y}rem ${spacing_x}rem`,
    'background-color': color,
    color: text_color,
}));
</script>
