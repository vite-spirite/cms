<template>
    <UButton :block="collapsed" class="w-full" color="error" icon="i-lucide-log-out" label="Logout" variant="ghost" @click="logout" />
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps<{ collapsed: boolean }>();

const logout = () => {
    router.post(route('admin.logout'));
};
</script>
