<template>
    <div>
        <UEditor v-slot="{ editor }" v-model="block.data.content" :editable="editable" content-type="markdown">
            <UEditorToolbar v-if="editable" v-show="selected" :editor="editor" :items="items" class="justify-center" />
            <UEditorToolbar v-if="editable" :editor="editor" :items="items" layout="bubble" />
        </UEditor>
    </div>
</template>

<script lang="ts" setup>
import { usePageBuilderStore } from '@modules/PageBuilder/Stores/usePageBuilderStore';
import type { EditorToolbarItem } from '@nuxt/ui';

const { id, editable, selected } = defineProps<{
    id: string;
    content: string;
    align: string;
    editable: boolean;
    selected: boolean;
}>();

const store = usePageBuilderStore();
const block = store.findBlockById(id);

const items: EditorToolbarItem[][] = [
    [
        {
            icon: 'i-lucide-heading',
            tooltip: { text: 'Headings' },
            content: {
                align: 'start',
            },
            items: [
                {
                    kind: 'heading',
                    level: 1,
                    icon: 'i-lucide-heading-1',
                    label: 'Heading 1',
                },
                {
                    kind: 'heading',
                    level: 2,
                    icon: 'i-lucide-heading-2',
                    label: 'Heading 2',
                },
                {
                    kind: 'heading',
                    level: 3,
                    icon: 'i-lucide-heading-3',
                    label: 'Heading 3',
                },
                {
                    kind: 'heading',
                    level: 4,
                    icon: 'i-lucide-heading-4',
                    label: 'Heading 4',
                },
            ],
        },
    ],
    [
        {
            kind: 'mark',
            mark: 'bold',
            icon: 'i-lucide-bold',
            tooltip: { text: 'Bold' },
        },
        {
            kind: 'mark',
            mark: 'italic',
            icon: 'i-lucide-italic',
            tooltip: { text: 'Italic' },
        },
        {
            kind: 'mark',
            mark: 'underline',
            icon: 'i-lucide-underline',
            tooltip: { text: 'Underline' },
        },
        {
            kind: 'mark',
            mark: 'strike',
            icon: 'i-lucide-strikethrough',
            tooltip: { text: 'Strikethrough' },
        },
        {
            kind: 'mark',
            mark: 'code',
            icon: 'i-lucide-code',
            tooltip: { text: 'Code' },
        },
    ],
];
</script>
