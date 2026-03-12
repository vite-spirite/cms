<template>
    <UModal class="w-full" description="choose in list" title="Select media:">
        <template #body>
            <UPageCard>
                <img v-for="media in medias" :key="media.id" :src="media.url" @click="emits('close', media)" />
            </UPageCard>
        </template>
    </UModal>
</template>

<script lang="ts" setup>
import { useApi } from '@modules/Module/Composables/useApi';
import type { Media } from '../types/media';
import { onMounted, ref } from 'vue';

const api = useApi();
const medias = ref<Media[]>([]);

const emits = defineEmits<{
    (e: 'close', media: Media | undefined): void;
}>();

onMounted(async () => {
    medias.value = await api.get<Media[]>('/api/gallery/list');
});
</script>
