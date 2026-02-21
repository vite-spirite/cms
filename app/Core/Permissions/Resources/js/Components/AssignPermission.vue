<template>
    <UCard v-if="allows" :ui="{ body: '!p-0' }">
        <template #header>
            <h2 class="text-xl font-semibold capitalize">Permissions assignment:</h2>
        </template>
        <div class="grid">
            <div v-for="category in permissionCategories" class="grid">
                <USeparator />
                <h2 class="p-4 font-semibold capitalize">{{ category }}:</h2>
                <USeparator />
                <UCheckboxGroup
                    v-model="form.extensions.permissions"
                    :items="permissionItems(category)"
                    :ui="{ fieldset: 'grid grid-cols-4 gap-4 p-4', item: 'flex flex-row justify-center items-center' }"
                />
            </div>
        </div>
    </UCard>
</template>

<script lang="ts" setup>
import type { useForm } from '@inertiajs/vue3';
import type { Permission } from '../types/role';
import type { User } from '@/types';

import { computed, onMounted, ref } from 'vue';
import { useApi } from '@modules/Module/Composables/useApi';
import { useGate } from '@modules/Module/Composables/useGate';

import PermissionApiGetController from '@/actions/App/Core/Permissions/Controllers/PermissionApiGetController';
import PermissionApiAllController from '@/actions/App/Core/Permissions/Controllers/PermissionApiAllController';

const gate = useGate();
const api = useApi();

const allows = computed(() => gate.can('permission_assign'));

const { form, user } = defineProps<{ form: ReturnType<typeof useForm>; user?: User }>();
const permissions = ref<Record<string, Permission[]>>({} as Record<string, Permission[]>);

form.extensions.permissions = [];

const permissionCategories = computed(() => Object.keys(permissions.value));
const permissionItems = (category: string) =>
    permissions.value[category].map((p) => ({ label: p.display_name, description: p.description, value: p.name }));
onMounted(async () => {
    permissions.value = await api.get<Permission[]>(PermissionApiAllController.url());

    if (user) {
        const userPermissions: Permission[] = await api.get<Permission[]>(PermissionApiGetController.url({ id: user.id }));
        form.extensions.permissions = userPermissions.map((p) => p.name);
    }
});
</script>
