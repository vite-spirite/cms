<template>
    <div class="relative">
        <div
            v-if="editable"
            :data-selected="store.selectedBlock?.id === block.id"
            class="absolute top-1/2 left-2 z-10 flex -translate-y-1/2 flex-col items-center justify-center space-y-1.5 rounded-md bg-elevated/75 p-2 opacity-0 data-[selected=true]:opacity-100"
        >
            <UIcon class="handle size-6 text-neutral-500" name="i-lucide-grip-vertical" />
            <UButton color="neutral" icon="i-lucide-x" size="sm" variant="link" @click.stop="store.removeById(block.id)" />
        </div>

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
        >
            <template v-if="block.data && block.data?.children" #default="{ containerClass, containerStyle }">
                <sortable
                    v-if="editable"
                    :key="store.dragVersion"
                    :class="containerClass"
                    :list="block.data.children"
                    :options="{ group: 'page_builder', chosenClass: 'chosen', handle: '.handle' }"
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

<style scoped>
@reference 'tailwindcss';

.chosen {
    @apply opacity-10 transition-all;
}
</style>
