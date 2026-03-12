<template>
    <UFormField :label="label" class="w-full">
        <div class="flex w-full items-center justify-start space-x-2">
            <UInput v-model="model" class="w-full" />
            <UButton color="neutral" icon="i-lucide-images" variant="outline" @click.prevent="open" />
        </div>
    </UFormField>
</template>
<script lang="ts" setup>
import ListMediaModal from './ListMediaModal.vue';
import type { Media } from '../types/media';

const model = defineModel({ required: true });
const { label } = defineProps<{ label: string }>();
const overlay = useOverlay();

const modal = overlay.create(ListMediaModal);

const open = async () => {
    const instance = modal.open();
    const response = (await instance.result) as Media | undefined;

    if (response) {
        model.value = response.url;
    }
};
</script>
