<template>
    <div>
        <slot :container-class="classes" :container-style="styles" />
    </div>
</template>

<script lang="ts" setup>
import type { PageBlock } from '../types';
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';

const { space_y, children, editable } = defineProps<{
    id: string;
    space_y: number;
    children: PageBlock[];
    editable: boolean;
    selected: boolean;
}>();

const classes = computed(() => twMerge(`w-full flex flex-col justify-start items-start`, children.length === 0 ? 'min-h-16 min-w-sm' : ''));

const styles = computed(() => ({
    gap: `${space_y}px`,
}));
</script>
