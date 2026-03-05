<!-- BlockSettings.vue -->
<template>
    <div v-if="store.selectedBlock" class="flex flex-col items-start justify-start space-y-2">
        <component
            :is="fieldComponents[field.type]"
            v-for="(field, key) in schema"
            :key="key"
            v-model="store.selectedBlock.data[key]"
            :label="field.label"
            :options="field.options"
        />
    </div>
</template>

<script lang="ts" setup>
import { type Component, computed } from 'vue';

import { usePageBuilderStore } from '../Stores/usePageBuilderStore';
import ColorInput from './Fields/ColorInput.vue';
import NumberInput from './Fields/NumberInput.vue';
import SelectInput from './Fields/SelectInput.vue';
import TextareaInput from './Fields/TextareaInput.vue';
import TextInput from './Fields/TextInput.vue';

const fieldComponents: Record<string, Component> = {
    text: TextInput,
    richtext: TextareaInput,
    select: SelectInput,
    int: NumberInput,
    // image: resolveComponent('UInput'),
    color: ColorInput,
};

const store = usePageBuilderStore();

const props = defineProps<{
    definition: any;
}>();

const schema = computed(() => {
    const def = props.definition;
    return def?.schema ?? {};
});
</script>
