<template>
    <UDashboardPanel id="role_list">
        <template #header>
            <UDashboardNavbar title="Roles list">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <UPage class="container mx-auto grid gap-6">
                <div v-if="hasRoleCreate" class="text-right">
                    <UButton :to="route('permissions.roles.create')" variant="soft">Create new role</UButton>
                </div>

                <UTable :columns="columns" :data="data" class="flex-1" />
            </UPage>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import Layout from '@/Layout/Dashboard.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, h, ref, resolveComponent, type VNode } from 'vue';
import { route } from 'ziggy-js';
import type { TableColumn } from '@nuxt/ui';
import type { Role as RoleType } from '../types/role';

type Role = {
    id: number;
    name: string;
    users_count: number;
    permissions_count: number;
};

const UButton = resolveComponent('UButton');
const page = usePage();
defineOptions({ layout: Layout });

const roles = computed(() => page.props.roles as RoleType[]);
const hasRoleCreate = computed(() => page.props.has_role_create as boolean);
const hasRoleDelete = computed(() => page.props.has_role_delete as boolean);
const hasRoleEdit = computed(() => page.props.has_role_edit as boolean);

console.log(page.props);

const data = ref<Role[]>(
    roles.value.map((role) => ({
        id: role.id,
        name: role.name,
        permissions_count: role.permissions_count ?? 0,
        users_count: role.users_count ?? 0,
    })),
);

const columns: TableColumn<Role>[] = [
    {
        accessorKey: 'id',
        header: '#',
    },
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'permissions_count',
        header: 'Permissions count',
    },
    {
        accessorKey: 'users_count',
        header: 'Users count',
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const forUserOptions: VNode[] = [];

            if (hasRoleDelete.value) {
                const node = h(UButton, {
                    color: 'error',
                    variant: 'soft',
                    icon: 'i-lucide-trash',
                    onClick: () =>
                        router.delete(route('permissions.roles.delete', { id: row.original.id }), { preserveState: false, preserveScroll: true }),
                });

                forUserOptions.push(node);
            }

            if (hasRoleEdit.value) {
                const node = h(UButton, {
                    color: 'info',
                    variant: 'soft',
                    icon: 'i-lucide-pen',
                    onClick: () => router.get(route('permissions.roles.edit', { role: row.original.id })),
                });

                forUserOptions.push(node);
            }

            return h('div', { class: 'flex justify-end items-center gap-2' }, forUserOptions);
        },
    },
];
</script>
