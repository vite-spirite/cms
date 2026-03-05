<template>
    <UCard v-if="allow" variant="soft">
        <template #header>
            <h2 class="text-xl font-semibold capitalize">Users :</h2>
        </template>

        <UTable :columns="columns" :data="data" />
    </UCard>
</template>

<script lang="ts" setup>
import { router, usePage } from '@inertiajs/vue3';
import { useGate } from '@modules/Module/Composables/useGate';
import type { TableColumn } from '@nuxt/ui';
import { computed, h, resolveComponent } from 'vue';
import type { User } from '@/types';

const UCheckbox = resolveComponent('UCheckbox');
const page = usePage();
const gate = useGate();
const allow = gate.can('role_assign');

const users = computed<User[]>(() => (page.props.users as User[]) ?? []);

const props = defineProps<{ members?: User[] }>();
const extensionValues = defineModel<Record<string, unknown>>({ required: true });
extensionValues.value.users = props.members ? (props.members.map((v) => v.id) as number[]) : ([] as number[]);

router.reload({
    only: ['users'],
});

const data = computed(() =>
    users.value.map((user) => ({
        id: user.id,
        name: user.name,
        email: user.email,
    })),
);

const columns: TableColumn<{ id: number; name: string; email: string }>[] = [
    {
        id: 'select',
        cell: ({ row }) => {
            return h(UCheckbox, {
                modelValue: (extensionValues.value.users as number[]).includes(row.original.id),
                'onUpdate:modelValue': (value: boolean) => {
                    row.toggleSelected(value);

                    if (value) {
                        extensionValues.value.users.push(row.original.id);
                    } else {
                        const index = extensionValues.value.users.findIndex((e: number) => e === row.original.id);
                        extensionValues.value.users.splice(index, 1);
                    }
                },
            });
        },
    },
    {
        accessorKey: 'name',
        header: 'Name:',
    },
    {
        accessorKey: 'email',
        header: 'Email:',
    },
];
</script>
