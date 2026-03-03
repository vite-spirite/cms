<template>
    <li><UButton :icon="item.icon" :label="item.label" variant="ghost" /></li>
    <template v-if="item.children">
        <ul>
            <TreeNode v-for="child in item.children" :key="child.id" :item="child" />
        </ul>
    </template>
</template>

<script lang="ts" setup>
import type { TreeItem } from '@nuxt/ui';

const { item } = defineProps<{ item: TreeItem }>();
</script>
