<!-- BlockSettings.vue -->
<template>
    <div v-if="selectedBlock" class="flex flex-col items-start justify-start space-y-2">
        <component
            :is="fieldComponents[field.type]"
            v-for="(field, key) in schema"
            :key="key"
            v-model="selectedBlock.data[key]"
            :label="field.label"
            :options="field.options"
        />
    </div>
</template>

<script lang="ts" setup>
import { type Component, computed } from 'vue';

import SelectInput from './Fields/SelectInput.vue';
import TextareaInput from './Fields/TextareaInput.vue';
import TextInput from './Fields/TextInput.vue';
import NumberInput from './Fields/NumberInput.vue';
import ColorInput from './Fields/ColorInput.vue';

const fieldComponents: Record<string, Component> = {
    text: TextInput,
    richtext: TextareaInput,
    select: SelectInput,
    int: NumberInput,
    // image: resolveComponent('UInput'),
    color: ColorInput,
};

const props = defineProps<{
    selectedBlock: any;
    definition: any;
}>();

const schema = computed(() => {
    const def = props.definition;
    return def?.schema ?? {};
});
</script>
