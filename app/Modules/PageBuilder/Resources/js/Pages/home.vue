<template>
    <UDashboardPanel id="page_list">
        <template #header>
            <UDashboardNavbar title="Pages:">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <UPage>
                <div class="container mx-auto flex flex-col space-y-4">
                    <div v-if="gate.can('page_create')" class="w-full text-right">
                        <UButton color="success" icon="i-lucide-plus" label="Create new page" to="/admin/page/create" variant="soft" />
                    </div>

                    <UTable :columns="columns" :data="pages" />
                </div>
            </UPage>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import { router, usePage } from '@inertiajs/vue3';
import { useGate } from '@modules/Module/Composables/useGate';
import type { TableColumn } from '@nuxt/ui';
import { format } from 'date-fns';
import type { VNode } from 'vue';
import { computed, h, resolveComponent } from 'vue';

import Layout from '@/Layout/Dashboard.vue';
import { route } from 'ziggy-js';
import type { Page, PageStatus } from '../types';

defineOptions({ layout: Layout });

const page = usePage<{ pages: Page[] }>();
const gate = useGate();

const pages = computed<Page[]>(() => page.props.pages);
const UBadge = resolveComponent('UBadge');
const UButton = resolveComponent('UButton');

const statusBadgeColor: Record<PageStatus, string> = {
    published: 'success',
    draft: 'warning',
    archived: 'error',
};

const columns: TableColumn<Page>[] = [
    {
        accessorKey: 'id',
        header: '#',
    },
    {
        accessorKey: 'title',
        header: 'Title',
    },
    {
        accessorKey: 'slug',
        header: 'Url',
        cell: ({ row }) =>
            h(UButton, {
                variant: 'link',
                to: route('page.render', { slug: row.original.slug }),
                label: route('page.render', { slug: row.original.slug }),
                target: '_blank',
            }),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) =>
            h(UBadge, {
                label: row.original.status,
                variant: 'outline',
                color: statusBadgeColor[row.original.status],
                size: 'md',
            }),
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
        cell: ({ row }) => format(row.original.created_at, 'P'),
    },
    {
        accessorKey: 'updated_at',
        header: 'Updated at',
        cell: ({ row }) => (row.original.updated_at !== row.original.created_at ? format(row.original.updated_at, 'P') : 'N/A'),
    },
    {
        id: 'actions',
        header: 'Actions:',
        cell: ({ row }) => {
            const components: VNode[] = [];

            if (gate.can('page_edit')) {
                const element = h(UButton, {
                    variant: 'soft',
                    color: 'info',
                    icon: 'i-lucide-pencil',
                    to: route('page.edit', { id: row.original.id }),
                });

                components.push(element);
            }

            if (gate.can('page_delete')) {
                const element = h(UButton, {
                    variant: 'soft',
                    color: 'error',
                    icon: 'i-lucide-trash',
                    onClick: () => router.delete(route('page.delete', { id: row.original.id })),
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
