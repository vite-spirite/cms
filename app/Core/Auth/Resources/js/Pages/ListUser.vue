<template>
    <UDashboardPanel id="list_user">
        <template #header>
            <UDashboardNavbar title="Users list:">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <UPage class="container mx-auto">
                <div v-if="gate.can('user_create')" class="w-full text-right">
                    <UButton :to="route('admin.users.create')" color="success" leading-icon="i-lucide-plus" variant="soft">Create user</UButton>
                </div>

                <UTable :columns="columns" :data="users" />
            </UPage>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>

import { router, usePage } from '@inertiajs/vue3';
import { useGate } from '@modules/Module/Composables/useGate';
import type { TableColumn } from '@nuxt/ui';
import { format } from 'date-fns';
import { computed, h, resolveComponent, type VNode } from 'vue';
import Layout from '@/Layout/Dashboard.vue';
import type { User } from '@/types';
import { route } from 'ziggy-js';


defineOptions({ layout: Layout });

const UButton = resolveComponent('UButton');
const page = usePage();
const gate = useGate();
const users = computed(() => page.props.users);

const columns: TableColumn<User>[] = [
    {
        accessorKey: 'id',
        header: '#',
    },
    {
        accessorKey: 'name',
        header: 'Name:',
    },
    {
        accessorKey: 'email',
        header: 'Email:',
    },
    {
        accessorKey: 'created_at',
        header: 'Created at:',
        cell: ({ row }) => {
            return format(row.original.created_at, 'P');
        },
    },
    {
        id: 'actions',
        header: 'Actions:',
        cell: ({ row }) => {
            const components: VNode[] = [];

            if (gate.can('user_edit')) {
                const element = h(UButton, {
                    variant: 'soft',
                    color: 'info',
                    icon: 'i-lucide-pencil',
                    to: route('admin.users.edit', { id: row.original.id }),
                });

                components.push(element);
            }

            if (gate.can('user_delete')) {
                const element = h(UButton, {
                    variant: 'soft',
                    color: 'error',
                    icon: 'i-lucide-trash',
                    onClick: () => router.delete(route('admin.users.delete', { id: row.original.id })),
                });

                components.push(element);
            }

            return h(
                'div',
                {
                    class: 'flex items-center space-x-2',
                },
                components,
            );
        },
    },
];
</script>
