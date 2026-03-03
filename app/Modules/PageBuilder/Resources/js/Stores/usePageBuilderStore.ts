import { defineStore } from 'pinia';
import { Definition, Page, PageBlock, PageBuilderSettings } from '../types';
import { ref } from 'vue';
import { v4 as uuidv4 } from 'uuid';

export const usePageBuilderStore = defineStore('pageBuilder', () => {
    const blocks = ref<PageBlock[]>([] as PageBlock[]);
    const settings = ref<PageBuilderSettings>({
        title: '',
        slug: '',
        status: 'draft',
        og_balises: {},
    });

    const selectedBlock = ref<PageBlock | null>(null);
    const selectedChildren = ref<number>(0);

    const dragging = ref<PageBlock | null>(null);
    const dragVersion = ref(0);

    const createBlock = (type: string, definition: Definition): PageBlock => {
        const data: Record<string, any> = {};

        Object.keys(definition.schema).forEach((key) => {
            const defaultValue = definition.schema[key].default ?? '';
            data[key] = typeof defaultValue === 'object' && defaultValue !== null ? JSON.parse(JSON.stringify(defaultValue)) : defaultValue;
        });

        return {
            id: uuidv4(),
            type,
            order: blocks.value.length,
            data: { ...data },
        };
    };

    const addBlock = (definition: Definition) => {
        if (selectedBlock.value && selectedBlock.value.data && typeof selectedBlock.value.data.children == 'object') {
            const block = createBlock(definition.type, definition);
            block.order = selectedBlock.value.data.children.length;

            selectedBlock.value.data.children.push(block);
            return;
        }

        blocks.value.push(createBlock(definition.type, definition));
    };

    const syncOrderInTree = (blocks: PageBlock[]): PageBlock[] => {
        return [...blocks].map((b, i) => {
            if (b.data?.children) {
                b.data.children = syncOrderInTree(b.data.children);
            }

            b.order = i + 1;
            return b;
        });
    };

    const syncOrder = () => {
        blocks.value = syncOrderInTree(blocks.value);
    };

    const selectBlock = (block: PageBlock | null) => {
        selectedBlock.value = block;
    };

    const deepFindById = (blocks: PageBlock[], id: string): PageBlock | null => {
        let block: PageBlock | null = null;

        for (const b of blocks) {
            if (b.id === id) {
                block = b;
                break;
            }

            if (b.data?.children) {
                const recursiveFind = deepFindById(b.data.children, id);
                if (recursiveFind) {
                    block = recursiveFind;
                    break;
                }
            }
        }

        return block;
    };

    const findBlockById = (id: string) => {
        return deepFindById(blocks.value, id);
    };

    const deepRemoveById = (blocks: PageBlock[], id: string) => {
        for (let i = 0; i < blocks.length; i++) {
            const block = blocks[i];

            if (block.id === id) {
                blocks.splice(i, 1);
                break;
            }

            if (block.data && block.data.children) {
                deepRemoveById(block.data.children, id);
            }
        }
    };

    const removeById = (id: string) => {
        deepRemoveById(blocks.value, id);
    };

    const serialize = () => {
        return {
            title: settings.value.title,
            slug: settings.value.slug,
            og_balises: settings.value.og_balises,
            status: settings.value.status,
            content: blocks.value,
        };
    };

    const onDragMove = (tree: PageBlock[], newIndex: number, oldIndex: number) => {
        const moved = tree.splice(oldIndex, 1)[0];
        tree.splice(newIndex, 0, moved);

        syncOrder();
    };
    const onDragRemove = (tree: PageBlock[], oldIndex: number) => {
        tree.splice(oldIndex, 1);
        syncOrder();
    };

    const onDragAdd = (tree: PageBlock[], newIndex: number) => {
        if (!dragging.value) {
            return;
        }

        tree.splice(newIndex, 0, dragging.value);
        syncOrder();
    };

    const onDragStart = (block: PageBlock) => {
        dragging.value = block;
    };

    const onDragEnd = () => {
        dragging.value = null;
        dragVersion.value++;
    };

    const hydrate = (page: Page) => {
        settings.value = {
            title: page.title,
            og_balises: page.og_balises,
            slug: page.slug,
            status: page.status,
        };

        blocks.value = page.content;

        dragVersion.value++;
    };

    const reset = () => {
        blocks.value = [];
        settings.value = {
            title: '',
            slug: '',
            status: 'draft',
            og_balises: {},
        };

        selectedBlock.value = null;
        selectedChildren.value = 0;

        dragging.value = null;
        dragVersion.value = 0;
    };

    return {
        blocks,
        settings,
        selectedBlock,
        selectedChildren,
        selectBlock,
        addBlock,
        syncOrder,
        findBlockById,
        serialize,
        onDragAdd,
        onDragEnd,
        onDragMove,
        onDragStart,
        onDragRemove,
        dragVersion,
        removeById,
        hydrate,
        reset,
    };
});
