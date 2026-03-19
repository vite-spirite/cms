<template>
    <a :id="id" :class="classes" :href="editable ? '#' : url" :style="styles">{{ label }}</a>
</template>

<script lang="ts" setup>
import { usePageBuilderStore } from '@modules/PageBuilder/Stores/usePageBuilderStore';
import { computed, watch } from 'vue';
import { twMerge } from 'tailwind-merge';

type Props = {
    id: string;
    editable: boolean;
    label: string;
    url: string;
    variant: 'solid' | 'outline' | 'ghost';
    size: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    bg_color: string;
    text_color: string;
    border: number;
    border_color: string;
    hover_bg_color: string;
    hover_border_color: string;
    hover_text_color: string;
    active_hovered_bg_color: boolean;
    active_hovered_border_color: boolean;
    active_hovered_text_color: boolean;
    bg_transparency: boolean;
};

const props = defineProps<Props>();

const store = usePageBuilderStore();
const currentBlock = computed(() => store.findBlockById(props.id));

const variants = {
    solid: {
        bg_color: '#4ade80',
        text_color: '#f5f5f5',
        border: 0,
        border_color: '#fff',
        hover_bg_color: '#22c55e',
        hover_border_color: '#fff',
        hover_text_color: '#fff',
        active_hovered_bg_color: true,
        active_hovered_border_color: false,
        active_hovered_text_color: false,
        bg_transparency: false,
    },
    outline: {
        bg_color: '#ffffff',
        text_color: '#4ade80',
        border: 2,
        border_color: '#4ade80',
        hover_bg_color: '#4ade80',
        hover_border_color: '#4ade80',
        hover_text_color: '#ffffff',
        active_hovered_bg_color: true,
        active_hovered_border_color: false,
        active_hovered_text_color: true,
        bg_transparency: false,
    },
    ghost: {
        bg_color: 'transparent',
        text_color: '#4ade80',
        border: 0,
        border_color: 'transparent',
        hover_bg_color: '#4ade80',
        hover_border_color: 'transparent',
        hover_text_color: '#ffffff',
        active_hovered_bg_color: false,
        active_hovered_border_color: false,
        active_hovered_text_color: false,
        bg_transparency: true,
    },
};

watch(
    () => props.variant,
    (val) => {
        if (!currentBlock.value) {
            return;
        }

        currentBlock.value.data = { ...currentBlock.value.data, ...variants[val] };
    },
);

const borderStyle = computed(() => {
    const base: Record<string, string> = {
        border: `${props.border}px solid`,
    };

    if (props.border_color) {
        base.borderColor = props.border_color;
    }

    if (props.active_hovered_border_color && props.hover_border_color) {
        base['--btn-border-hovered'] = props.hover_border_color;
    } else {
        base['--btn-border-hovered'] = props.border_color;
    }

    return base;
});

const colorsStyles = computed(() => {
    const base: Record<string, string> = {};

    if (!props.bg_transparency && props.bg_color) {
        base.backgroundColor = props.bg_color;
    }

    if (props.text_color) {
        base.color = props.text_color;
    }

    if (props.active_hovered_text_color && props.hover_text_color) {
        base['--btn-text-hovered'] = props.hover_text_color;
    } else {
        base['--btn-text-hovered'] = props.text_color;
    }

    if (props.active_hovered_bg_color && props.hover_bg_color) {
        base['--btn-bg-hovered'] = props.hover_bg_color;
    } else {
        base['--btn-bg-hovered'] = props.bg_color;
    }

    return base;
});

const sizeClasses = computed(
    () =>
        ({
            xs: 'px-2 py-1 text-xs gap-1',
            sm: 'px-2.5 py-1.5 text-xs gap-1.5',
            md: 'px-2.5 py-1.5 text-sm gap-1.5',
            lg: 'px-3 py-2 text-sm gap-2',
            xl: 'px-3 py-2 text-base gap-2',
        })[props.size],
);

const classes = computed(() => twMerge('rounded-md font-medium transition-colors', sizeClasses.value));
const styles = computed(() => ({ ...borderStyle.value, ...colorsStyles.value }));
</script>

<style scoped>
a:hover {
    border-color: var(--btn-border-hovered) !important;
    background-color: var(--btn-bg-hovered) !important;
    color: var(--btn-text-hovered) !important;
}
</style>
