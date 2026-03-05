<template>
    <UDrawer v-if="allow" v-model:open="open" fixed>
        <div v-show="!open" class="fixed bottom-0 left-0 z-1 flex w-full divide-x divide-default border-t border-default bg-default">
            <UButton class="flex items-center space-x-2 rounded-none" color="neutral" label="Console" variant="ghost">
                Console
                <UChip :text="logsCount.reduce((acc, v) => acc + v.count, 0)" color="error" inset size="3xl" standalone></UChip>
            </UButton>

            <UButton
                v-for="level in logsLevel"
                :key="level"
                :color="levelColors[level]"
                class="flex items-center space-x-2 rounded-none"
                variant="ghost"
            >
                {{ level }}
                <UChip :color="levelColors[level]" :text="logsCount.find((v) => v.level === level)?.count ?? 0" inset size="3xl" standalone></UChip>
            </UButton>
        </div>

        <template #content>
            <div class="max-h-196 min-h-48 overflow-auto">
                <UTable :columns="columns" :data="logs" :meta="meta">
                    <template #expanded="{ row }">
                        <pre v-highlight><code class="language-json">{{ row.original.context }}</code></pre>
                    </template>
                </UTable>
            </div>
        </template>
    </UDrawer>
</template>

<script lang="ts" setup>
import { router, usePage } from '@inertiajs/vue3';
import { useApi } from '@modules/Module/Composables/useApi';
import { useGate } from '@modules/Module/Composables/useGate';
import type { TableColumn } from '@nuxt/ui';
import type { Row, TableMeta } from '@tanstack/vue-table';
import { format } from 'date-fns';
import { computed, h, onMounted, onUnmounted, ref, resolveComponent } from 'vue';
import { route } from 'ziggy-js';
import { vHighlight } from '../Directives/highlightjs';
import type { Log, LogCount, LogLevel } from '../types/logs';

router.reload({ only: ['start_session_at'] });

const gate = useGate();
const allow = gate.can('logger_view');
const page = usePage();
const api = useApi();

const open = ref(false);
const since = computed(() => page.props.start_session_at);

const logs = ref<Log[]>([]);
const logsCount = ref<LogCount[]>([]);
const logsLevel = ref<LogLevel[]>(['success', 'error', 'warning', 'info', 'debug']);

const levelColors: Record<LogLevel, 'info' | 'warning' | 'error' | 'success' | 'neutral'> = {
    success: 'success',
    error: 'error',
    warning: 'warning',
    info: 'info',
    debug: 'neutral',
};

const getLogs = async () => {
    if (!allow) {
        return;
    }

    const fetchedLogs = await api.get<{ logs: Log[]; counts: LogCount[] }>(route('api.logger.since', { since: since.value }));

    logs.value = fetchedLogs.logs;
    logsCount.value = fetchedLogs.counts;

    setTimeout(getLogs, 10000);
};

onMounted(async () => {
    if (allow) {
        document.documentElement.style.setProperty('--logger-bar-height', '32px');
        await getLogs();
    }
});

onUnmounted(() => {
    document.documentElement.style.removeProperty('--logger-bar-height');
});

const UBadge = resolveComponent('UBadge');
const UButton = resolveComponent('UButton');

const columns: TableColumn<Log>[] = [
    {
        id: 'expand',
        cell: ({ row }) =>
            h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                icon: 'i-lucide-chevron-down',
                square: true,
                'aria-label': 'Expand',
                ui: {
                    leadingIcon: ['transition-transform', row.getIsExpanded() ? 'duration-200 rotate-180' : ''],
                },
                onClick: () => row.toggleExpanded(),
            }),
    },
    {
        accessorKey: 'level',
        header: 'level',
        cell: ({ row }) => {
            const icon: string = {
                debug: 'i-lucide-bug',
                info: 'i-lucide-info',
                warning: 'i-lucide-triangle-alert',
                error: 'i-lucide-circle-x',
                success: 'i-lucide-circle-check',
            }[row.original.level];

            const color: string = levelColors[row.original.level];

            return h(UBadge, {
                icon: icon,
                label: row.original.level,
                color: color,
                variant: 'soft',
            });
        },
    },
    {
        accessorKey: 'category',
        header: 'Category:',
    },
    {
        accessorKey: 'action',
        header: 'Action:',
    },
    {
        accessorKey: 'user.name',
        header: 'Username:',
    },
    {
        accessorKey: 'message',
        header: 'Message:',
    },
    {
        accessorKey: 'url',
        header: 'URL:',
    },
    {
        accessorKey: 'created_at',
        header: 'Date:',
        cell: ({ row }) => {
            return format(row.original.created_at, 'P pp');
        },
    },
];

const meta: TableMeta<Log> = {
    class: {
        tr: (row: Row<Log>) => {
            return {
                debug: 'bg-elevated/10',
                info: 'bg-info/10',
                warning: 'bg-warning/10',
                error: 'bg-error/10',
                success: 'bg-success/10',
            }[row.original.level];
        },
    },
};
</script>

<style>
@reference 'tailwindcss';

#dashboard {
    @apply h-[calc(100%-var(--logger-bar-height))];
}

#dashboard > * {
    @apply max-h-full! min-h-full!;
}

#test {
    @apply text-red-500;
}
</style>
