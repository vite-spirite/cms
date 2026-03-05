<template>
    <component :is="ext" v-for="(ext, index) in extensions" :key="index" v-model="model" v-bind="extensionProps" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import ExtensionRegistry from '../ExtensionRegistry';

const { name, extensionProps } = defineProps<{ name: string; extensionProps?: Record<string, any> }>();
const extensions = computed(() => ExtensionRegistry.resolve(name));

const model = defineModel<Record<string, unknown>>();
</script>
