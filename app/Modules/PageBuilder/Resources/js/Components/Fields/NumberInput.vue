<template>
    <UFormField :label="label" class="w-full">
        <UInputNumber v-model="model" class="w-full" />
    </UFormField>
</template>

<script lang="ts" setup>
const { label } = defineProps<{ label: string }>();
const model = defineModel({ default: 0 });
</script>
