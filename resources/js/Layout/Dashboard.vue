<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';

import ExtensionPoint from '@modules/Module/Components/ExtensionPoint.vue';
import { computed, watch } from 'vue';

const page = usePage();
const success = computed(() => page.props.success as { title: string; description: string; icon?: string } | undefined);
const error = computed(() => page.props.error as { title: string; description: string; icon?: string } | any | undefined);

const toast = useToast();

watch(success, (val) => {
    if (val) {
        toast.add({
            title: val.title,
            icon: val.icon ?? 'i-lucide-bell-ring',
            description: val.description,
            color: 'success',
        });
    }
});

watch(error, (val) => {
    if (val && val.title && val.description) {
        toast.add({
            title: val.title,
            description: val.description,
            icon: val.icon ?? 'i-lucide-bell-ring',
            color: 'error',
        });
    }
});
</script>

<template>
    <UApp>
        <UDashboardGroup>
            <ExtensionPoint name="layout.dashboard.left" />
            <slot />
        </UDashboardGroup>
    </UApp>
</template>
