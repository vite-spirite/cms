<template>
    <UButton :block="collapsed" class="w-full" color="error" icon="i-lucide-log-out" label="Logout" variant="ghost" @click="logout" />
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import LogoutController from '@/actions/App/Core/Auth/Controllers/LogoutController';

defineProps<{ collapsed: boolean }>();

const logout = () => {
    router.post(LogoutController.url());
};
</script>
