<template>
    <UFormField :label="label" class="flex w-full flex-row items-center justify-between">
        <USwitch v-model="model" class="w-full" />
    </UFormField>
</template>

<script lang="ts" setup>
const model = defineModel({ default: false });
const { label } = defineProps<{ label: string }>();
</script>
