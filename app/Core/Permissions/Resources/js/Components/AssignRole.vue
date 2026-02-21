<template>
    <UCard v-if="allow">
        <template #header>
            <h2 class="text-xl font-semibold capitalize">Roles assignment:</h2>
        </template>
        <UCheckboxGroup
            v-model="form.extensions.roles"
            :items="roleItems"
            :ui="{ fieldset: 'grid grid-cols-4 gap-4', item: 'flex flex-row justify-center' }"
        />
    </UCard>
</template>

<script lang="ts" setup>
import type { useForm } from '@inertiajs/vue3';
import type { User } from '@/types';
import type { Role } from '../types/role';

import { computed, onMounted, ref } from 'vue';
import { useApi } from '@modules/Module/Composables/useApi';
import { useGate } from '@modules/Module/Composables/useGate';

import RoleApiAllController from '@/actions/App/Core/Permissions/Controllers/RoleApiAllController';
import RoleApiGetController from '@/actions/App/Core/Permissions/Controllers/RoleApiGetController';

const gate = useGate();
const api = useApi();

const allow = computed(() => gate.can('role_assign'));

const { form, user } = defineProps<{ form: ReturnType<typeof useForm>; user?: User }>();
const roles = ref<Role[]>([] as Role[]);

form.extensions.roles = [];

const roleItems = computed(() => roles.value.map((r) => ({ value: r.id, label: r.name })));

onMounted(async () => {
    roles.value = await api.get<Role[]>(RoleApiAllController.url());

    if (user) {
        const userRoles: Role[] = await api.get<Role[]>(RoleApiGetController.url({ id: user.id }));
        form.extensions.roles = userRoles.map((r) => r.id);
    }
});
</script>
