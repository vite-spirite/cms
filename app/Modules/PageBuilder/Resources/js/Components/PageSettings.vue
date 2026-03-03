<template>
    <div class="flex flex-col items-start justify-start space-y-2">
        <UFormField class="w-full" label="Page title:">
            <UInput v-model="store.settings.title" class="w-full" type="text" />
        </UFormField>

        <UFormField class="w-full" label="Page slug:">
            <UInput v-model="store.settings.slug" class="w-full" type="text" />
        </UFormField>

        <UFormField class="w-full" label="Page status:">
            <USelect v-model="store.settings.status" :items="items" class="w-full" />
        </UFormField>

        <UFormField :ui="{ container: 'flex w-full flex-col items-start justify-start space-y-2' }" class="w-full" label="Page og balises:">
            <div v-for="(balise, index) in og_balises" class="relative grid w-full grid-cols-2 gap-2">
                <UFormField class="w-full" label="Balise name:">
                    <UInput v-model="balise.name" class="w-full" />
                </UFormField>
                <UFormField class="w-full" label="Balise value:">
                    <UInput v-model="balise.content" class="w-full" />
                </UFormField>

                <UButton
                    class="absolute top-0 right-0 rounded-full"
                    color="error"
                    icon="i-lucide-x"
                    size="xs"
                    variant="ghost"
                    @click="removeBalise(index)"
                />
            </div>

            <UButton block class="w-full" color="success" icon="i-lucide-plus" label="Add balise" variant="soft" @click="addEmptyBalise" />
        </UFormField>
    </div>
</template>

<script lang="ts" setup>
import { PageStatus } from '../types';
import { onMounted, ref, watch } from 'vue';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';

const store = usePageBuilderStore();
const items = ref<PageStatus[]>(['draft', 'published', 'archived']);

const og_balises = ref<{ name: string; content: string }[]>([]);

const addEmptyBalise = () => {
    og_balises.value.push({ name: '', content: '' });
};

const removeBalise = (index: number) => {
    og_balises.value.splice(index, 1);
};

const hydrateBalises = () => {
    store.settings.og_balises = {};

    og_balises.value.forEach((v) => {
        if (v.name.length == 0) {
            return;
        }

        store.settings.og_balises[v.name] = v.content;
    });
};

watch(og_balises, () => hydrateBalises(), { deep: true });

onMounted(() => {
    og_balises.value = [];
    Object.keys(store.settings.og_balises).forEach((k) => {
        og_balises.value.push({
            name: k,
            content: store.settings.og_balises[k],
        });
    });
});
</script>
