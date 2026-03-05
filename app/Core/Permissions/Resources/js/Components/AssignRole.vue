<template>
    <UCard v-if="allow">
        <template #header>
            <h2 class="text-xl font-semibold capitalize">Roles assignment:</h2>
        </template>
        <UCheckboxGroup
            v-model="extensionValues.roles"
            :items="roleItems"
            :ui="{ fieldset: 'grid grid-cols-4 gap-4', item: 'flex flex-row justify-center' }"
        />
    </UCard>
</template>

<script lang="ts" setup>
import { useApi } from '@modules/Module/Composables/useApi';
import { useGate } from '@modules/Module/Composables/useGate';
import { computed, onMounted, ref } from 'vue';
import type { User } from '@/types';
import { route } from 'ziggy-js';
import type { Role } from '../types/role';

const gate = useGate();
const api = useApi();

const allow = computed(() => gate.can('role_assign'));

const { user } = defineProps<{ user?: User }>();
const extensionValues = defineModel<Record<string, unknown>>({ required: true });
const roles = ref<Role[]>([] as Role[]);

extensionValues.value.roles = [];

const roleItems = computed(() => roles.value.map((r) => ({ value: r.id, label: r.name })));

onMounted(async () => {
    roles.value = await api.get<Role[]>(route('api.roles.all'));

    if (user) {
        const userRoles: Role[] = await api.get<Role[]>(route('api.roles.get', { id: user.id }));
        extensionValues.value.roles = userRoles.map((r) => r.id);
    }
});
</script>
