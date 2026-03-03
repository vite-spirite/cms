<template>
    <img :alt="alt" :src="url" class="w-full" />
</template>

<script lang="ts" setup>
const { url, alt } = defineProps<{ url: string; alt: string }>();
</script>
