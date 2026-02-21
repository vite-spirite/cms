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
import { computed, h, resolveComponent } from 'vue';
import type { User } from '@/types';
import { TableColumn } from '@nuxt/ui';
import { useGate } from '@modules/Module/Composables/useGate';

const UCheckbox = resolveComponent('UCheckbox');
const page = usePage();
const gate = useGate();
const allow = gate.can('role_assign');

const users = computed<User[]>(() => (page.props.users as User[]) ?? []);

const props = defineProps<{ form: { extensions: Record<string, any> }; members?: User[] }>();
props.form.extensions.users = props.members ? props.members.map((v) => v.id) : [];

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
                modelValue: props.form.extensions.users.includes(row.original.id),
                'onUpdate:modelValue': (value: boolean) => {
                    row.toggleSelected(!!value);

                    if (value) {
                        props.form.extensions.users.push(row.original.id);
                    } else {
                        const index = props.form.extensions.users.findIndex((e: number) => e === row.original.id);
                        props.form.extensions.users.splice(index, 1);
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
