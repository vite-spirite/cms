<template>
    <div>
        <slot :container-class="classes" :container-style="styles" />
    </div>
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';
import type { PageBlock } from '../types';

const { space_x, children } = defineProps<{
    id: string;
    space_x: number;
    children: PageBlock[];
    editable: boolean;
    selected: boolean;
}>();

const classes = computed(() => twMerge(`w-full flex justify-start items-start p-2`, children.length === 0 ? 'min-h-16' : ''));

const styles = computed(() => ({
    gap: `${space_x}px`,
}));
</script>
