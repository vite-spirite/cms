<template>
    <div
        :data-selected="store.selectedBlock?.id === block.id"
        :data-visible="store.hoveredBlock?.id === block.id && store.selectedBlock === null"
        class="data-[selected=true]: data-[visible=true]: absolute top-1/2 left-2 z-10 hidden -translate-y-1/2 flex-col items-center justify-center space-y-1.5 rounded-md bg-elevated/75 p-2 opacity-0 transition-all data-[selected=true]:flex data-[selected=true]:opacity-100 data-[visible=true]:flex data-[visible=true]:opacity-100"
    >
        <UIcon class="handle size-6 cursor-grab text-neutral-500" name="i-lucide-grip-vertical" title="drag" />

        <UButton
            v-if="isParentSelectable"
            color="neutral"
            icon="i-lucide-corner-left-up"
            size="sm"
            title="Select parent"
            variant="link"
            @click="store.selectParentBlock(block)"
        />

        <UButton color="neutral" icon="i-lucide-copy" size="sm" title="duplicate" variant="link" @click.stop="store.duplicateBlock(block)" />
        <UButton color="neutral" icon="i-lucide-trash" size="sm" title="delete" variant="link" @click.stop="store.removeById(block.id)" />
        <UButton color="neutral" icon="i-lucide-x" size="sm" title="unselect" variant="link" @click.stop="store.selectBlock(null)" />
    </div>
</template>

<script lang="ts" setup>
import { usePageBuilderStore } from '@modules/PageBuilder/Stores/usePageBuilderStore';
import type { PageBlock } from '@modules/PageBuilder/types';
import { computed } from 'vue';

const { block } = defineProps<{ block: PageBlock }>();
const store = usePageBuilderStore();

const isParentSelectable = computed(() => store.isChildBlock(block));
</script>
