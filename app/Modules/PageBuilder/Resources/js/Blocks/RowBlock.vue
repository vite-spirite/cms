<template>
    <slot :container-class="classes" :container-style="styles" />
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';
import type { PageBlock } from '../types';

const { gap, children, align_items, justify_content, wrap } = defineProps<{
    id: string;
    gap: number;
    align_items: 'start' | 'center' | 'end' | 'stretch';
    justify_content: 'start' | 'center' | 'end' | 'between';
    wrap: boolean;
    children: PageBlock[];
    editable: boolean;
    selected: boolean;
}>();

const alignItemsClass = computed(
    () =>
        ({
            start: 'align-start',
            center: 'align-center',
            end: 'align-end',
            stretch: 'align-stretch',
        })[align_items],
);

const justifyContentClass = computed(
    () =>
        ({
            start: 'justify-start',
            center: 'justify-center',
            end: 'justify-end',
            between: 'justify-between',
        })[justify_content],
);

const classes = computed(() =>
    twMerge(
        `w-full flex flex-row`,
        alignItemsClass.value,
        justifyContentClass.value,
        wrap ? 'flex-wrap' : 'flex-nowrap',
        children.length === 0 ? 'min-h-16' : '',
    ),
);
const styles = computed(() => ({
    gap: `${gap * 4}px`,
}));
</script>
