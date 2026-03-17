# Frontend Conventions

Each module can ship its own Vue components, pages, stores, and registries. This page covers the conventions and entry
points that the frontend build system expects.

## Directory structure

```
app/Modules/MyModule/Resources/js/
├── extensions.ts       # Extension point registrations
├── blocks.ts           # PageBuilder block registrations
├── fields.ts           # PageBuilder field registrations
├── Pages/              # Inertia pages
├── Components/         # Vue components
├── Stores/             # Pinia stores
├── Composables/        # Vue composables
└── types/              # TypeScript types
```

## Entry files

### `extensions.ts`

Registers Vue components onto extension points. This file is automatically loaded by the build system.

```ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import MyComponent from './Components/MyComponent.vue';

ExtensionRegistry.register('users.create.end', MyComponent);
```

See [Extension Points](/modules/extension-points) for the full list of available points.

### `blocks.ts`

Registers PageBuilder block components. Only needed if your module provides blocks for the page builder.

```ts
import BlockRegistry from '@modules/PageBuilder/blockRegistry';

BlockRegistry.register('my-block', () => import('./Blocks/MyBlock.vue'));
```

The first argument must match the `type()` method of the corresponding PHP block class.

### `fields.ts`

Registers custom field components for the PageBuilder block settings panel. Only needed if your blocks use custom field
types.

```ts
import FieldRegistry from '@modules/PageBuilder/fieldRegistry';

FieldRegistry.register('my-field', () => import('./Components/Fields/MyField.vue'));
```

## Path aliases

The following aliases are available in all modules:

| Alias                  | Resolves to                            |
|------------------------|----------------------------------------|
| `@modules/Module`      | `app/Core/Module/Resources/js`         |
| `@modules/PageBuilder` | `app/Modules/PageBuilder/Resources/js` |
| `@/`                   | `resources/js` (global app)            |

## Inertia pages

Pages are rendered by Inertia using a module namespace. The namespace is defined in the PHP service provider and maps to
the `Pages/` directory of your module.

```php
// In a controller
return Inertia::render('MyModule::MyPage');
```

```
app/Modules/MyModule/Resources/js/Pages/MyPage.vue
```

Every page that uses the dashboard layout should declare it via `defineOptions`:

```vue

<script lang="ts" setup>
    import Layout from '@/Layout/Dashboard.vue';

    defineOptions({layout: Layout});
</script>
```

## Composables

Two composables are available globally from the Module core:

### `useGate`

Check permissions on the frontend. See [Permissions](/modules/core/permissions#checking-permissions) for full details.

```ts
import {useGate} from '@modules/Module/Composables/useGate';

const gate = useGate();
gate.can('user_create');
gate.canAny(['user_create', 'user_edit']);
```

### `useApi`

A lightweight fetch wrapper that handles CSRF tokens automatically, for calling internal API routes.

```ts
import {useApi} from '@modules/Module/Composables/useApi';

const api = useApi();

// GET request, returns parsed JSON
const data = await api.get<MyType>('/api/my-route');
```

::: warning
`useApi` only supports `GET` requests. For mutations, use Inertia's `useForm` or `router` instead.
:::

## TypeScript types

Global shared types are defined in `resources/js/types/index.ts`. The `User` type is available across all modules:

```ts
import type {User} from '@/types';
```

Define your own module-specific types in `Resources/js/types/`:

```ts
// app/Modules/MyModule/Resources/js/types/index.ts
export interface MyModel {
    id: number;
    name: string;
}
```

## Registries

The frontend has three registries, all following the same pattern: `register(key, component)` and `resolve(key)`.

### ExtensionRegistry

Maps extension point names to arrays of Vue components.

```ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';

ExtensionRegistry.register('point.name', MyComponent);
ExtensionRegistry.resolve('point.name'); // Component[]
```

### BlockRegistry

Maps PageBuilder block type strings to async Vue components.

```ts
import BlockRegistry from '@modules/PageBuilder/blockRegistry';

BlockRegistry.register('my-block', () => import('./Blocks/MyBlock.vue'));
BlockRegistry.resolve('my-block'); // AsyncComponent
```

### FieldRegistry

Maps field type strings to async Vue components for the PageBuilder settings panel.

```ts
import FieldRegistry from '@modules/PageBuilder/fieldRegistry';

FieldRegistry.register('my-field', () => import('./Components/Fields/MyField.vue'));
FieldRegistry.resolve('my-field'); // AsyncComponent
```

::: tip
`BlockRegistry` and `FieldRegistry` use `defineAsyncComponent` internally, so your imports are automatically
lazy-loaded.
:::
