<template>
    <UFormField :label="label" class="w-full">
        <USelect v-model="model" :items="options" class="w-full" />
    </UFormField>
</template>

<script lang="ts" setup>
const model = defineModel<string>({ required: true });
const { label, options } = defineProps<{ label: string; options: string[] }>();
</script>
