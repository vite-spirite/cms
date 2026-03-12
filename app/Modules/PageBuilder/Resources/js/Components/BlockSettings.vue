<!-- BlockSettings.vue -->
<template>
    <div v-if="store.selectedBlock" class="flex flex-col items-start justify-start space-y-2">
        <component
            :is="FieldRegistry.resolve(field.type)"
            v-for="(field, key) in schema"
            :key="key"
            v-model="store.selectedBlock.data[key]"
            :label="field.label"
            :options="field.options"
        />
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { usePageBuilderStore } from '../Stores/usePageBuilderStore';
import FieldRegistry from '@modules/PageBuilder/fieldRegistry';

const store = usePageBuilderStore();

const props = defineProps<{
    definition: any;
}>();

const schema = computed(() => {
    const def = props.definition;
    return def?.schema ?? {};
});
</script>
