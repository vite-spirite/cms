<template>
    <UFormField :label="label" class="w-full">
        <UTextarea v-model="model" class="w-full" />
    </UFormField>
</template>

<script lang="ts" setup>
const model = defineModel<string>({ required: true });
const { label } = defineProps<{ label: string }>();
</script>
