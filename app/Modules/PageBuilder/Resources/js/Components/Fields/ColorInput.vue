<template>
    <UFormField :label="label" class="w-full">
        <div class="flex items-center justify-between gap-2">
            <UInput v-model="model" class="w-full">
                <template #leading>
                    <span :style="chip" class="size-3 rounded-full"></span>
                </template>
            </UInput>

            <UPopover>
                <UButton icon="i-lucide-palette"></UButton>

                <template #content>
                    <UColorPicker v-model="model" class="w-full" format="hex" />
                </template>
            </UPopover>
        </div>
    </UFormField>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const model = defineModel<string>({ required: true });
const { label } = defineProps<{ label: string }>();

const chip = computed(() => ({ backgroundColor: model.value }));
</script>
