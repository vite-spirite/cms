<template>
    <p :style="{ textAlign: align as any }" v-html="content"></p>
</template>

<script lang="ts" setup>
const { content, align } = defineProps<{ content: string; align: string }>();
</script>
