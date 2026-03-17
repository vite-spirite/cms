# Extension Points

Extension points allow modules to inject Vue components into pages of other modules without any direct dependency. They
are the primary way to extend the UI of existing pages.

## How it works

An **extension point** is a named slot in a Vue page or layout. Any module can register one or more components on that
slot via the `ExtensionRegistry`. When the page renders, it resolves all components registered on that point and renders
them in order.

```
Module A defines:
<ExtensionPoint name="users.create.end" v-model="form.extensions" />

Module B registers:
ExtensionRegistry.register('users.create.end', MyComponent)

→ MyComponent is rendered inside Module A's page
```

## Registering a component

Create or update the `extensions.ts` file in your module's `Resources/js` directory:

```ts
// app/Modules/MyModule/Resources/js/extensions.ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import MyComponent from './Components/MyComponent.vue';

ExtensionRegistry.register('users.create.end', MyComponent);

// A single component can be registered on multiple points
ExtensionRegistry.register('users.edit.end', MyComponent);
```

::: tip
The `extensions.ts` file is automatically picked up by the frontend build. Make sure it is imported somewhere in your
module's entry point.
:::

## Writing a component for an extension point

Every component registered on an extension point receives:

- **`v-model`** — a shared `Record<string, unknown>` object. All components on the same point share the same model. Use
  it to pass data back to the parent form.
- **Any additional props** defined in the `extension-props` attribute of the `ExtensionPoint` component.

```vue

<template>
    <UCard>
        <p>My extension component</p>
    </UCard>
</template>

<script lang="ts" setup>
    // Always declare the model even if you don't use it
    const extensionValues = defineModel<Record<string, unknown>>({required: true});

    // Write your data into the shared extensions payload
    extensionValues.value.myKey = 'myValue';
</script>
```

When additional props are passed via `extension-props`, declare them as regular props:

```vue

<script lang="ts" setup>
    import type {User} from '@/types';

    // Additional prop passed by the extension point
    const {user} = defineProps<{ user?: User }>();

    const extensionValues = defineModel<Record<string, unknown>>({required: true});
</script>
```

## Defining an extension point

If you are building a module and want to allow other modules to inject content into your pages, use the `ExtensionPoint`
component:

```vue

<template>
    <form @submit.prevent="onSubmit">
        <!-- Other form fields -->

        <!-- Allow other modules to inject content at the end of the form -->
        <ExtensionPoint v-model="form.extensions" name="mymodule.form.end"/>

        <UButton type="submit">Submit</UButton>
    </form>
</template>

<script lang="ts" setup>
    import {useForm} from '@inertiajs/vue3';
    import ExtensionPoint from '@modules/Module/Components/ExtensionPoint.vue';

    const form = useForm({
        // ...
        extensions: {} as Record<string, any>,
    });
</script>
```

### Passing extra props to injected components

Use `extension-props` to pass contextual data to all components registered on the point:

```vue

<ExtensionPoint
    v-model="form.extensions"
    name="mymodule.form.end"
    :extension-props="{ user: currentUser }"
/>

```

## Available extension points

### Layout

| Point name                | Location                                  | Props passed |
|---------------------------|-------------------------------------------|--------------|
| `layout.dashboard.left`   | Left sidebar slot of the dashboard layout | —            |
| `layout.dashboard.bottom` | Bottom slot of the dashboard layout       | —            |

### Auth module

| Point name           | Location                       | Props passed  |
|----------------------|--------------------------------|---------------|
| `users.create.start` | Top of the create user form    | —             |
| `users.create.end`   | Bottom of the create user form | —             |
| `users.edit.start`   | Top of the edit user form      | `user` (User) |
| `users.edit.end`     | Bottom of the edit user form   | `user` (User) |

### Permissions module

| Point name               | Location                       | Props passed       |
|--------------------------|--------------------------------|--------------------|
| `role.create.form.start` | Top of the create role form    | —                  |
| `role.create.form.end`   | Bottom of the create role form | —                  |
| `role.update.form.start` | Top of the edit role form      | `members` (User[]) |
| `role.update.form.end`   | Bottom of the edit role form   | `members` (User[]) |

### Navigation module

| Point name                  | Location              | Props passed          |
|-----------------------------|-----------------------|-----------------------|
| `navigation.sidebar.footer` | Bottom of the sidebar | `collapsed` (boolean) |

## Who registers on what

Here is an overview of all existing registrations across the codebase:

| Component           | Registered on                                  | Module      |
|---------------------|------------------------------------------------|-------------|
| `LogoutButton`      | `navigation.sidebar.footer`                    | Auth        |
| `AssignRole` (user) | `users.create.end`, `users.edit.end`           | Permissions |
| `AssignPermission`  | `users.create.end`, `users.edit.end`           | Permissions |
| `AssignRole` (role) | `role.create.form.end`, `role.update.form.end` | Auth        |
| `DashboardSidebar`  | `layout.dashboard.left`                        | Navigation  |
| `LoggerBar`         | `layout.dashboard.bottom`                      | Logger      |
