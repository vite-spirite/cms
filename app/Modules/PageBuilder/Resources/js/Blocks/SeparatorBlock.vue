<template>
    <div :style="{ borderStyle: borderStyle, borderColor: color, borderWidth: `${width}px` }" class="block h-px w-full border-t" />
</template>

<script lang="ts" setup>
const {
    color,
    border_style: borderStyle,
    width,
} = defineProps<{
    color: string;
    border_style: 'dashed' | 'dotted' | 'inset' | 'solid' | 'double' | 'groove' | 'ridge' | 'outset';
    width: number;
}>();

console.log(color, borderStyle);
</script>
