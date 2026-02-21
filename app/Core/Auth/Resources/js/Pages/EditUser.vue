<template>
    <UDashboardPanel id="create_user">
        <template #header>
            <UDashboardNavbar title="Create user">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <form class="container mx-auto grid gap-6" @submit.prevent="onSubmit">
                <ExtensionPoint :extension-props="{ form, user: page.props.user }" name="users.edit.start" />

                <UCard>
                    <template #header>
                        <h2 class="text-xl font-semibold capitalize">Information's:</h2>
                    </template>

                    <div class="grid grid-cols-2 gap-6">
                        <UFormField :error="page.props.form_errors.email" class="w-full" label="Email:">
                            <UInput v-model="form.email" class="w-full" placeholder="Email..." type="email" variant="soft" />
                        </UFormField>

                        <UFormField :error="page.props.form_errors.name" class="w-full" label="Name:">
                            <UInput v-model="form.name" class="w-full" placeholder="Name..." variant="soft" />
                        </UFormField>

                        <UFormField :error="page.props.form_errors.password" class="w-full" label="Password:">
                            <UInput v-model="form.password" class="w-full" placeholder="Password..." type="password" variant="soft" />
                        </UFormField>

                        <UFormField :error="page.props.form_errors.password_confirmation" class="w-full" label="Password confirmation:">
                            <UInput
                                v-model="form.password_confirmation"
                                class="w-full"
                                placeholder="Password confirmation..."
                                type="password"
                                variant="soft"
                            />
                        </UFormField>
                    </div>
                </UCard>

                <ExtensionPoint :extension-props="{ form, user: page.props.user }" name="users.edit.end" />

                <div class="w-full text-right">
                    <UButton class="justify-center" color="info" label="Edit user" type="submit" />
                </div>
            </form>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import type { User } from '@/types';

import { useForm, usePage } from '@inertiajs/vue3';

import ExtensionPoint from '@modules/Module/Components/ExtensionPoint.vue';
import EditUserRequestController from '@/actions/App/Core/Auth/Controllers/EditUserRequestController';
import Layout from '@/Layout/Dashboard.vue';

defineOptions({ layout: Layout });

type PageProps = {
    form_errors: Record<string, string>;
    user: User;
};

const page = usePage<PageProps>();
const form = useForm({
    id: page.props.user.id,
    email: page.props.user.email,
    name: page.props.user.name,
    password: '',
    password_confirmation: '',
    extensions: {} as Record<string, any>,
});

const onSubmit = () => {
    form.put(EditUserRequestController.url());
};
</script>
