<template>
    <component
        :is="resolvedComponent"
        v-if="resolvedComponent"
        :id="block.id"
        :data-selected="store.selectedBlock?.id === block.id"
        :editable="editable"
        :selected="store.selectedBlock?.id === block.id"
        class="transition-all"
        v-bind="block.data"
        @click.stop="() => store.selectBlock(block)"
        @mouseenter.stop="store.setHovered(block)"
        @mouseleave.stop="store.setHovered(null)"
    >
        <template v-if="block.data && block.data?.children" #default="{ containerClass, containerStyle }">
            <sortable
                v-if="editable"
                :key="store.dragVersion"
                :class="containerClass"
                :list="block.data.children"
                :options="{ group: 'page_builder', chosenClass: 'chosen', handle: '.handle', animation: 150 }"
                :style="containerStyle"
                itemKey="id"
                @add="(e: SortableEvent) => store.onDragAdd(block.data.children, e.newIndex ?? 0)"
                @end="
                    (e: SortableEvent) => {
                        if (e.from === e.to) {
                            store.onDragMove(block.data.children, e.newIndex ?? 0, e.oldIndex ?? 0);
                        }

                        store.onDragEnd();
                    }
                "
                @remove="(e: SortableEvent) => store.onDragRemove(block.data.children, e.oldIndex ?? 0)"
                @start="(e: SortableEvent) => store.onDragStart(block.data.children[e.oldIndex ?? 0])"
            >
                <template #item="{ element }">
                    <div class="relative">
                        <BlockRender :block="element" :editable="editable" />
                        <BlockToolbar :block="element" />
                    </div>
                </template>
            </sortable>

            <div v-else :class="`${containerClass}`" :style="containerStyle">
                <BlockRender v-for="child in block.data.children" :key="child.id" :block="child" :editable="editable" />
            </div>
        </template>
    </component>
    <div v-else class="block-unknown">Bloc "{{ block.type }}" introuvable</div>
</template>

<script lang="ts" setup>
import blockRegistry from '@modules/PageBuilder/blockRegistry';
import BlockToolbar from '@modules/PageBuilder/Components/BlockToolbar.vue';
import type { SortableEvent } from 'sortablejs';
import { Sortable } from 'sortablejs-vue3';
import { computed } from 'vue';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';

const props = defineProps<{ block: any; editable: boolean }>();
const resolvedComponent = computed(() => blockRegistry.resolve(props.block.type));
const store = usePageBuilderStore();
</script>

<style scoped>
@reference 'tailwindcss';

.chosen {
    @apply opacity-50 transition-all;
}
</style>
