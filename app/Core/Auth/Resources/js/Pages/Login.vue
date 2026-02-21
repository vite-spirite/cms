<template>
    <UApp>
        <div class="flex min-h-screen flex-col items-center justify-center">
            <UPageCard class="w-full max-w-sm">
                <UAuthForm :fields="fields" :schema="schema" icon="i-lucide-user" title="Login" @submit="onSubmit"></UAuthForm>
            </UPageCard>
        </div>
    </UApp>
</template>

<script lang="ts" setup>
import z from 'zod';
import type { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
import { useForm } from '@inertiajs/vue3';
import LoginRequestController from '@/actions/App/Core/Auth/Controllers/LoginRequestController';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const schema = z.object({
    email: z.email(),
    password: z.string().min(8),
    remember: z.boolean().optional(),
});

type Schema = z.output<typeof schema>;

const fields: AuthFormField[] = [
    {
        type: 'email',
        label: 'Email',
        required: true,
        name: 'email',
    },
    {
        type: 'password',
        label: 'Password',
        name: 'password',
        required: true,
    },
    {
        type: 'checkbox',
        name: 'remember',
        label: 'Remember me',
        required: false,
        defaultValue: false,
    },
];

const onSubmit = (event: FormSubmitEvent<Schema>) => {
    form.email = event.data.email;
    form.password = event.data.password;
    form.remember = event.data.remember ?? false;

    form.post(LoginRequestController.url());
};
</script>

<style scoped></style>
