<template>
    <div :class="widthClass">
        <slot :container-class="classes" :container-style="styles" />
    </div>
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';
import type { PageBlock } from '../types';

const { gap, width, align_items, children } = defineProps<{
    id: string;
    gap: number;
    width: 'auto' | '1/2' | '1/3' | '2/3' | '1/4' | '3/4' | 'full';
    align_items: 'start' | 'center' | 'end';
    children: PageBlock[];
    editable: boolean;
    selected: boolean;
}>();

const widthClass = computed(
    () =>
        ({
            auto: 'w-auto',
            '1/2': 'w-1/2',
            '1/3': 'w-1/3',
            '2/3': 'w-2/3',
            '1/4': 'w-1/4',
            '3/4': 'w-3/4',
            full: 'w-full',
        })[width],
);

const alignItemsClass = computed(
    () =>
        ({
            start: 'justify-start',
            center: 'justify-center',
            end: 'justify-end',
        })[align_items],
);

const classes = computed(() =>
    twMerge(`w-full flex flex-col justify-start items-start`, alignItemsClass.value, children.length === 0 ? 'min-h-16 min-w-sm' : ''),
);

const styles = computed(() => ({
    gap: `${gap * 4}px`,
}));
</script>
