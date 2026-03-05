<template>
    <div class="relative">
        <div
            v-if="editable"
            :data-selected="store.selectedBlock?.id === block.id"
            class="pointer-events-none absolute inset-0 z-2 opacity-0 outline-2 outline-blue-500 hover:opacity-50 data-[selected=true]:opacity-100"
        >
            <div class="pointer-events-auto absolute top-0 left-0 flex gap-1 bg-blue-500 px-2 py-0.5 text-xs text-white">
                <span>{{ block.type }}</span>
                <button @click.stop="store.removeById(block.id)">✕</button>
            </div>
        </div>

        <component
            :is="resolvedComponent"
            v-if="resolvedComponent"
            :id="block.id"
            :editable="editable"
            :selected="store.selectedBlock?.id === block.id"
            v-bind="block.data"
            @click.stop="() => store.selectBlock(block)"
        >
            <template v-if="block.data && block.data?.children" #default="{ containerClass, containerStyle }">
                <sortable
                    v-if="editable"
                    :key="store.dragVersion"
                    :class="containerClass"
                    :list="block.data.children"
                    :options="{ group: 'page_builder' }"
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
                        <BlockRender :block="element" :editable="editable" />
                    </template>
                </sortable>

                <div v-else :class="`${containerClass}`" :style="containerStyle">
                    <BlockRender v-for="child in block.data.children" :key="child.id" :block="child" :editable="editable" />
                </div>
            </template>
        </component>
        <div v-else class="block-unknown">Bloc "{{ block.type }}" introuvable</div>
    </div>
</template>

<script lang="ts" setup>
import blockRegistry from '@modules/PageBuilder/blockRegistry';
import type { SortableEvent } from 'sortablejs';
import { Sortable } from 'sortablejs-vue3';
import { computed } from 'vue';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';

const props = defineProps<{ block: any; editable: boolean }>();
const resolvedComponent = computed(() => blockRegistry.resolve(props.block.type));
const store = usePageBuilderStore();
</script>
