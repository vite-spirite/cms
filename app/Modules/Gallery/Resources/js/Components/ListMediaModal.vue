<template>
    <UModal description="choose in list" title="Select media:">
        <template #body>
            <div class="full container grid grid-cols-2 gap-2">
                <img
                    v-for="media in medias"
                    :key="media.id"
                    :src="media.url"
                    :ui="{ body: 'sm:p-0 p-0' }"
                    class="rounded-md"
                    @click="emits('close', media)"
                />
            </div>
        </template>
    </UModal>
</template>

<script lang="ts" setup>
import { useApi } from '@modules/Module/Composables/useApi';
import { onMounted, ref } from 'vue';
import type { Media } from '../types/media';

const api = useApi();
const medias = ref<Media[]>([]);

const emits = defineEmits<{
    (e: 'close', media: Media | undefined): void;
}>();

onMounted(async () => {
    medias.value = await api.get<Media[]>('/api/gallery/list');
});
</script>
