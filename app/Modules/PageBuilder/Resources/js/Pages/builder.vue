<template>
    <UDashboardPanel id="components_list" :default-size="15" :max-size="20" :min-size="10" :ui="{ body: 'gap-2 sm:gap-2' }" resizable>
        <template #header>
            <UDashboardNavbar title="Components :">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <UButton
                v-for="(definition, type) in definitions"
                :key="type"
                :icon="definition.icon"
                :label="definition.label"
                color="neutral"
                variant="ghost"
                wide
                @click="store.addBlock(definition)"
            />
        </template>
    </UDashboardPanel>

    <UDashboardPanel id="page" :ui="{ body: 'p-0 sm:p-0' }">
        <template #header>
            <UDashboardNavbar title="Pages:">
                <template #right>
                    <UButton color="success" label="Save" variant="soft" @click="onSave" />
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <div class="min-h-full w-full overflow-auto bg-white text-black">
                <Sortable
                    :key="store.dragVersion"
                    :list="store.blocks"
                    :options="{ group: 'page_builder' }"
                    itemKey="id"
                    @add="(e: SortableEvent) => store.onDragAdd(store.blocks, e.newIndex ?? 0)"
                    @end="
                        (e: SortableEvent) => {
                            if (e.from === e.to) {
                                store.onDragMove(store.blocks, e.newIndex ?? 0, e.oldIndex ?? 0);
                            }
                            store.onDragEnd();
                        }
                    "
                    @remove="(e: SortableEvent) => store.onDragRemove(store.blocks, e.oldIndex ?? 0)"
                    @start="(e: SortableEvent) => store.onDragStart(store.blocks[e.oldIndex ?? 0])"
                >
                    <template #item="{ element }">
                        <BlockRender :block="element" :editable="true" class="cursor-pointer transition-all duration-200 hover:opacity-75" />
                    </template>
                </Sortable>
            </div>
        </template>
    </UDashboardPanel>

    <UDashboardPanel id="component_settings" :default-size="15" :max-size="20" :min-size="10" resizable>
        <template #header>
            <UDashboardNavbar title="Settings :">
                <template #right>
                    <UButton v-if="store.selectedBlock" color="neutral" icon="i-lucide-x" variant="ghost" @click="store.selectedBlock = null" />
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <BlockSettings v-if="store.selectedBlock" :definition="definitions[store.selectedBlock.type]" :selected-block="store.selectedBlock" />
            <PageSettings v-else />
            <USeparator />
            <PageTree :definitions="definitions" />
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import { useForm, usePage } from '@inertiajs/vue3';
import type { SortableEvent } from 'sortablejs';
import { Sortable } from 'sortablejs-vue3';
import { computed, onUnmounted } from 'vue';
import Layout from '@/Layout/Dashboard.vue';
import { route } from 'ziggy-js';
import BlockRender from '../Components/BlockRender.vue';
import BlockSettings from '../Components/BlockSettings.vue';
import PageSettings from '../Components/PageSettings.vue';
import PageTree from '../Components/PageTree.vue';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';
import type { Definition, Page } from '../types';

defineOptions({ layout: Layout });

const page = usePage<{ blocks: Record<string, Definition>; page?: Page }>();
const definitions = computed(() => page.props.blocks);
const store = usePageBuilderStore();

const form = useForm({
    title: '',
    slug: '',
    status: 'draft',
    og_balises: {},
    content: [] as any[],
});

const onSave = () => {
    const data = store.serialize();

    form.title = data.title;
    form.slug = data.slug;
    form.og_balises = data.og_balises;
    form.content = data.content as any[];
    form.status = data.status;

    if (!page.props.page) {
        form.post(route('page.create.request'));
    } else {
        form.put(route('page.edit.request', { id: page.props.page.id }));
    }
};

if (page.props.page) {
    store.hydrate(page.props.page, definitions.value);
}

onUnmounted(() => {
    store.reset();
});
</script>
