<template>
    <section :class="sectionClasses" :style="styles">
        <div v-if="bg_image && bg_overlay" :style="{ background: `rgba(0,0,0,${bg_overlay / 100})` }" class="absolute inset-0"></div>

        <div :class="classes" :style="styles">
            <slot :container-class="slotClasses" />
        </div>
    </section>
</template>

<script lang="ts" setup>
import { twMerge } from 'tailwind-merge';
import { computed } from 'vue';
import type { PageBlock } from '../types';

const { max_width, spacing_x, spacing_y, bg_type, bg_color, bg_image, text_color, force_height } = defineProps<{
    id: string;
    selected: boolean;
    editable: boolean;
    max_width: 'sm' | 'md' | 'xl' | 'full';
    spacing_x: number;
    spacing_y: number;
    bg_type: 'color' | 'image';
    bg_color?: string;
    bg_image?: string;
    bg_overlay?: number;
    text_color: string;
    force_height: boolean;
    children: PageBlock[];
}>();

const max_width_class = computed(
    () =>
        ({
            sm: 'max-w-screen-sm',
            md: 'max-w-screen-md',
            xl: 'max-w-screen-xl',
            full: 'max-w-full',
        })[max_width],
);

const sectionClasses = computed(() => twMerge('relative w-full', force_height ? 'min-h-screen' : undefined));
const classes = computed(() => twMerge('mx-auto w-full', max_width_class.value));

const styles = computed(() => {
    const base: Record<string, string> = {
        padding: `${spacing_y}rem ${spacing_x}rem`,
        color: text_color,
    };

    if (bg_type === 'color' && bg_color) {
        base.background = bg_color;
    }

    if (bg_type === 'image' && bg_image) {
        base.background = `url(${bg_image})`;
        base.bacgroundSize = 'cover';
        base.backgroundPosition = 'center';
    }

    return base;
});

const slotClasses = computed(() => 'flex flex-col w-full');
</script>
