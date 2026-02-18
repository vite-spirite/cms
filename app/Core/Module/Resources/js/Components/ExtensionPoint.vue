<template>
    <div class="w-full">
        <component :is="ext" v-for="(ext, index) in extensions" :key="index" v-bind="extensionProps" />
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import ExtensionRegistry from '../ExtensionRegistry';

const { name, extensionProps } = defineProps<{ name: string; extensionProps: Record<string, any> }>();
const extensions = computed(() => ExtensionRegistry.resolve(name));
</script>
