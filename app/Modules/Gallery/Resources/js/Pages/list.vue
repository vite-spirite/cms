<template>
    <UDashboardPanel id="media_panel">
        <template #body>
            <UPageGrid class="container mx-auto py-6">
                <div class="flex flex-col items-center justify-center space-y-3 lg:col-span-3">
                    <UFileUpload v-model="form.files" class="min-h-46 w-full" multiple />
                    <UButton block color="success" icon="i-lucide-upload" label="Upload" variant="soft" @click="submit" />
                </div>

                <UCard v-for="media in medias" :key="media.id" :ui="{ body: 'p-0 sm:p-0' }" class="col-span-1">
                    <template #header>
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col items-start justify-start rounded-md p-2">
                                <h5>Original name: {{ media.label }}</h5>
                                <h5>Uploaded by: {{ media.uploader?.name }}</h5>
                                <h5>Uploaded at: {{ formatDate(media.created_at) }}</h5>
                            </div>

                            <UButton color="error" icon="i-lucide-trash" label="Delete" variant="soft" @click="deleteButton(media)" />
                        </div>
                    </template>

                    <img :alt="media.label" :src="media.url" />
                </UCard>
            </UPageGrid>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed } from 'vue';
import Layout from '@/Layout/Dashboard.vue';
import { route } from 'ziggy-js';
import DeleteModal from '../Components/DeleteModal.vue';
import type { Media } from '../types/media';

defineOptions({ layout: Layout });

const page = usePage<{ medias: Media[] }>();
const overlay = useOverlay();
const medias = computed(() => page.props.medias);

const modal = overlay.create(DeleteModal);

const form = useForm({
    files: [],
});

const submit = () => {
    form.post(route('gallery.uploads'), { forceFormData: true });
};

const formatDate = (date: Date | string) => format(date, 'P p');

const deleteButton = async (media: Media) => {
    const instance = modal.open({
        media,
    });

    const response = await instance.result;

    if (response) {
        router.delete(route('gallery.delete', { media: media.id }));
    }
};
</script>
