<template>
    <UModal
        :title="`Are you sure you want to delete ${media.label} ?`"
        description="Deleting this media from the gallery is irreversible. Please note that if a page uses this media, it will no longer be displayed."
    >
        <template #footer>
            <div class="flex w-full items-center justify-end space-x-6">
                <UButton color="neutral" variant="soft" @click="emits('close', false)">Cancel</UButton>
                <UButton color="error" variant="soft" @click="emits('close', true)">Delete</UButton>
            </div>
        </template>
    </UModal>
</template>

<script lang="ts" setup>
import type { Media } from '../types/media';

const emits = defineEmits<{
    (e: 'close', state: boolean): void;
}>();

const { media } = defineProps<{ media: Media }>();
</script>
