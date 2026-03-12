<template>
    <img :alt="alt" :src="media" class="w-full" />
</template>

<script lang="ts" setup>
const { media, alt } = defineProps<{ media: string; alt: string }>();
</script>
