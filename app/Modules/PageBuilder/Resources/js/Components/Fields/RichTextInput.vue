<template>
    <UFormField :label="label" class="w-full">
        <UEditor v-slot="{ editor }" v-model="model" class="w-full rounded-md border border-default" content-type="markdown">
            <UEditorToolbar :editor="editor" :items="items" layout="bubble" />
        </UEditor>
    </UFormField>
</template>

<script lang="ts" setup>
import type { EditorToolbarItem } from '@nuxt/ui';

const model = defineModel<string>({ required: true });
const { label } = defineProps<{ label: string }>();

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
