<template>
    <UTree v-model="selected" :getKey="(e) => e.id" :items="tree" @select="onSelect" />
</template>

<script lang="ts" setup>
import type { TreeItem } from '@nuxt/ui';
import { computed, onMounted, ref, watch } from 'vue';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';
import type { Definition, PageBlock } from '../types';

const { definitions } = defineProps<{ definitions: Record<string, Definition> }>();

const store = usePageBuilderStore();
const tree = computed(() => {
    return recursiveTree(store.blocks);
});

const treeKey = computed(() => JSON.stringify(store.blocks.map((b) => ({ id: b.id, order: b.order }))));
const selected = ref<TreeItem | null>(null);

watch(
    () => store.selectedBlock,
    (val) => {
        if (!val) {
            selected.value = null;
            return;
        }

        selected.value = recursiveFind(tree.value, val.id);
    },
);

const recursiveTree = (blocks: PageBlock[]): TreeItem[] => {
    return blocks.map((b) => {
        const definition = definitions[b.type];

        if (!definition) {
            return { label: b.type };
        }

        return {
            id: b.id,
            label: definition.label,
            icon: definition.icon,
            children: b.data?.children ? recursiveTree(b.data.children as PageBlock[]) : [],
            defaultExpanded: true,
        };
    });
};

const recursiveFind = (items: TreeItem[], id: string): TreeItem | null => {
    let find: TreeItem | null = null;

    for (const item of items) {
        if (item.id === id) {
            find = item;
            break;
        }

        if (item.children && item.children.length > 0) {
            find = recursiveFind(item.children, id);
        }
    }

    return find;
};

const onSelect = (e: any) => {
    if (e.detail.originalEvent.type === 'click') {
        store.selectBlock(store.findBlockById(e.detail.value.id));
    }
};

onMounted(() => {
    if (store.selectedBlock) {
        selected.value = recursiveFind(tree.value, store.selectedBlock.id);
    }
});
</script>
