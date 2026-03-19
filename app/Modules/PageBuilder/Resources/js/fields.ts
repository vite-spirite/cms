import type { AsyncComponentLoader } from 'vue';
import FieldRegistry from './fieldRegistry';

const fields: { type: string; component: AsyncComponentLoader }[] = [
    {
        type: 'text',
        component: () => import('./Components/Fields/TextInput.vue'),
    },
    {
        type: 'richtext',
        component: () => import('./Components/Fields/RichTextInput.vue'),
    },
    {
        type: 'select',
        component: () => import('./Components/Fields/SelectInput.vue'),
    },
    {
        type: 'int',
        component: () => import('./Components/Fields/NumberInput.vue'),
    },
    {
        type: 'color',
        component: () => import('./Components/Fields/ColorInput.vue'),
    },
    {
        type: 'bool',
        component: () => import('./Components/Fields/BooleanField.vue'),
    },
];

FieldRegistry.registerMany(fields);
