# Navigation Module

The Navigation module manages the sidebar navigation of the admin dashboard. It is a **core module**, always loaded and
cannot be disabled.

## Overview

- Provides a `NavigationManager` singleton to register and retrieve navigation items
- Each module registers its own navigation items via `getNavigations()`
- Navigation is shared globally via Inertia on every page

## How it works

During the boot phase, each module's `BaseModuleServiceProvider` calls `registerNavigations()` which pushes its items
into the `NavigationManager`. Once all providers are booted, the full navigation tree is available and shared via
Inertia as `page.props.navigation`.

```
AppServiceProvider::boot()
└── loadModules() / loadStoredModules()
    └── each ModuleServiceProvider::boot()
        └── registerNavigations()
            └── NavigationManager::registerMany()
                └── NavigationItem::fromArray()

Inertia::share(['navigation' => NavigationManager::all()])
```

## Registering navigation items

Override `getNavigations()` in your module's service provider:

```php
public function getNavigations(): array
{
    return [
        [
            'label' => 'Blog',
            'icon'  => 'i-lucide-book-open',
            'route' => 'blog.index',
        ],
    ];
}
```

### With children

```php
public function getNavigations(): array
{
    return [
        [
            'label' => 'Blog',
            'icon'  => 'i-lucide-book-open',
            'children' => [
                ['label' => 'All posts',    'icon' => 'i-lucide-list',  'route' => 'blog.index'],
                ['label' => 'Create post',  'icon' => 'i-lucide-plus',  'route' => 'blog.create'],
            ],
        ],
    ];
}
```

## NavigationItem properties

| Property   | Type     | Required | Description                                         |
|------------|----------|----------|-----------------------------------------------------|
| `label`    | `string` | yes      | Text displayed in the sidebar                       |
| `icon`     | `string` | no       | Iconify icon name (e.g. `i-lucide-home`)            |
| `route`    | `string` | no       | Laravel route name, resolved to a URL automatically |
| `badge`    | `mixed`  | no       | Badge value, can be a callable for dynamic values   |
| `disabled` | `bool`   | no       | Disables the item, defaults to `false`              |
| `children` | `array`  | no       | Nested navigation items                             |
| `class`    | `string` | no       | Additional CSS classes                              |

::: tip
The `route` property is automatically resolved to a full URL using `URL::route()` so you only need to provide the route
name.
:::

::: tip
The `badge` property accepts a callable, which is evaluated at render time:

```php
'badge' => fn() => Post::where('status', 'draft')->count(),
```

:::

## Accessing navigation in Vue

The navigation tree is available on every page via Inertia shared props:

```ts
import {usePage} from '@inertiajs/vue3';

const page = usePage();
const navigation = page.props.navigation;
```

It is consumed by the `DashboardSidebar` component which passes it directly to `UNavigationMenu`.

## Extension Points

The Navigation module registers the `DashboardSidebar` component on the `layout.dashboard.left` extension point.

It also exposes the following extension point inside the sidebar itself:

| Point name                  | Location              | Props passed          |
|-----------------------------|-----------------------|-----------------------|
| `navigation.sidebar.footer` | Bottom of the sidebar | `collapsed` (boolean) |

**Example — the Auth module uses this to inject the logout button:**

```ts
// app/Core/Auth/Resources/js/extensions.ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import LogoutButton from './Components/LogoutButton.vue';

ExtensionRegistry.register('navigation.sidebar.footer', LogoutButton);
```

```vue

<script lang="ts" setup>
    defineProps<{ collapsed: boolean }>();
</script>
```
