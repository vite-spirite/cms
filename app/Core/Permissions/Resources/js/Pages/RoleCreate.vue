<template>
    <UDashboardPanel id="create_role">
        <template #header>
            <UDashboardNavbar title="Create roles">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <UPage class="container mx-auto grid gap-6">
                <form class="grid w-full gap-3" @submit.prevent="onSubmit">
                    <ExtensionPoint :extension-props="{ form }" name="role.create.form.start" />

                    <UFormField label="Name:">
                        <UInput v-model="form.name" class="w-full" />
                    </UFormField>

                    <UCard v-for="category in categories" :key="category" variant="soft">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold capitalize">{{ category }}</h2>

                                <div class="flex flex-row items-center gap-3">
                                    <UButton color="success" variant="soft" @click="bulkSelect(permissions[category])">Select all</UButton>
                                    <UButton color="error" variant="soft" @click="bulkDeselect(permissions[category])">Unelect all</UButton>
                                </div>
                            </div>
                        </template>

                        <UCheckboxGroup
                            v-model="form.permissions"
                            :items="formatItemsPermissions(permissions[category])"
                            :ui="{ fieldset: 'grid grid-cols-4 gap-4', item: 'flex flex-row justify-center items-center' }"
                        />
                    </UCard>

                    <ExtensionPoint :extension-props="{ form }" name="role.create.form.end" />

                    <div class="text-right">
                        <UButton class="justify-center" type="submit">Create role</UButton>
                    </div>
                </form>
            </UPage>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Layout from '@/Layout/Dashboard.vue';
import type { Permission } from '../types/role';
import type { CheckboxGroupItem } from '@nuxt/ui';
import RoleCreateRequestController from '@/actions/App/Core/Permissions/Controllers/RoleCreateRequestController';
import ExtensionPoint from '@modules/Module/Components/ExtensionPoint.vue';

defineOptions({ layout: Layout });

const page = usePage();
const permissions = computed(() => page.props.availablePermissions as Record<string, Permission[]>);
const categories = computed(() => Object.keys(permissions.value));

const form = useForm<{ name: string; permissions: string[]; extensions: Record<string, any> }>({
    name: '',
    permissions: [],
    extensions: {},
});

const onSubmit = () => {
    form.post(RoleCreateRequestController.url());
};

const formatItemsPermissions = (permissions: Permission[]): CheckboxGroupItem[] => {
    return permissions.map((permission) => ({
        label: permission.display_name ?? permission.name,
        description: permission.description,
        value: permission.name,
    }));
};

const bulkSelect = (permissions: Permission[]) => {
    const permissionValues = permissions.map((p) => p.name);
    form.permissions.push(...permissionValues);

    form.permissions = form.permissions.reduce((acc, item) => {
        if (!acc.includes(item)) acc.push(item);
        return acc;
    }, [] as string[]);
};

const bulkDeselect = (permissions: Permission[]) => {
    const permissionValues = permissions.map((p) => p.name);

    form.permissions = form.permissions.reduce((acc, item) => {
        if (!permissionValues.includes(item)) acc.push(item);
        return acc;
    }, [] as string[]);
};
</script>
