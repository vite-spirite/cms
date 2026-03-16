# Navigation Module

A centralized navigation registry system for managing dashboard sidebar items across all modules. The Navigation module provides a service layer for registering navigation items that are shared with the frontend via Inertia props.

## Purpose

The Navigation module enables:
- **Centralized Registration**: A single service (`NavigationManager`) where all modules can register their navigation items
- **Hierarchical Structure**: Support for nested navigation items with parent-child relationships
- **Dynamic Badges**: Calculate badge values at runtime using callable functions
- **Route Integration**: Automatic URL resolution from route names using Laravel's routing system
- **Frontend Sharing**: Navigation data is automatically shared to Vue components via Inertia props

## NavigationManager

The core service for managing all navigation items in the system.

### Public Methods

```php
public function register(array|NavigationItem $item): void
```
Registers a single navigation item. Accepts either a `NavigationItem` object or an array that will be converted to one via `NavigationItem::fromArray()`.

```php
public function registerMany(array $items): void
```
Registers multiple navigation items in bulk. Iterates through each item and calls `register()`.

```php
public function all(): array
```
Returns all registered navigation items as an array of associative arrays (serialized format). Each `NavigationItem` is converted using `toArray()` and ready for frontend consumption. Returns keys are stripped with `values()` to maintain sequential indexing.

## NavigationItem

A value object representing a single navigation entry with all configuration for display and behavior.

### Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `$label` | `string` | Yes | Display text for the navigation item |
| `$icon` | `string\|null` | No | Icon identifier (e.g., `'i-lucide-home'`) for UI rendering |
| `$route` | `string\|null` | No | Laravel route name. Converted to URL in `toArray()` using `URL::route()` |
| `$badge` | `mixed\|null` | No | Value or callable. If callable, invoked during `toArray()` to compute badge value dynamically |
| `$disabled` | `bool` | No | Disables the navigation item in the UI (default: `false`) |
| `$children` | `NavigationItem[]\|null` | No | Array of nested `NavigationItem` objects for hierarchical navigation |
| `$class` | `string\|null` | No | CSS classes applied to the item for custom styling |

### Creating from Array

```php
$item = NavigationItem::fromArray([
    'label' => 'Dashboard',
    'icon' => 'i-lucide-home',
    'route' => 'dashboard',
    'badge' => null,
    'disabled' => false,
    'children' => null,
    'class' => null,
]);
```

The `fromArray()` method:
- Requires `label` (throws `InvalidArgumentException` if missing)
- Recursively converts nested `children` arrays to `NavigationItem` objects
- Uses null coalescing for optional properties
- Default `disabled` is `false`

### Serializing to Array

```php
$array = $item->toArray();
```

Returns an associative array ready for frontend consumption:

```php
[
    'label' => 'Dashboard',
    'icon' => 'i-lucide-home',
    'to' => 'http://example.com/dashboard',  // resolved URL from route
    'badge' => 5,                            // badge callable invoked
    'disabled' => false,
    'children' => [
        [
            'label' => 'Settings',
            'to' => 'http://example.com/dashboard/settings',
        ]
    ],
    'class' => null,
]
```

**Key transformations in `toArray()`:**
- `$route` is converted to a full URL via `URL::route($this->route, [], false)`
- Property name changes: `route` → `to` (frontend-friendly name)
- `$badge` is invoked if callable, otherwise returned as-is
- Null values are filtered out with `array_filter()`
- Nested children are recursively converted via `toArray()`

### Dynamic Badges Example

Badge values can be computed dynamically using callables:

```php
$item = NavigationItem::fromArray([
    'label' => 'Messages',
    'badge' => function() {
        return MessageRepository::unreadCount(); // computed at serialization time
    },
]);
```

The callable is only invoked during `toArray()`, allowing fresh data on each request.

## Module Registration

Modules register navigation items in their service provider by injecting `NavigationManager`:

```php
namespace App\Modules\MyModule\Providers;

use App\Core\Module\BaseModuleServiceProvider;
use App\Core\Navigation\Service\NavigationManager;

class MyModuleServiceProvider extends BaseModuleServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $navigationManager = $this->app->make(NavigationManager::class);

        $navigationManager->registerMany([
            [
                'label' => 'Users',
                'icon' => 'i-lucide-users',
                'route' => 'users.index',
                'badge' => fn() => \App\Models\User::count(),
            ],
            [
                'label' => 'Roles',
                'icon' => 'i-lucide-shield',
                'route' => 'roles.index',
                'children' => [
                    [
                        'label' => 'Permissions',
                        'route' => 'permissions.index',
                    ],
                ],
            ],
        ]);
    }
}
```

### Best Practices

- Register items in the `boot()` method after parent `boot()` is called
- Use array format for brevity; `NavigationItem::fromArray()` handles conversion
- Always provide a `label` — it is required
- Use Laravel route names for `route` field; URLs are resolved at request time
- Use icon identifiers compatible with your UI framework (e.g., Lucide icon names)
- Keep `children` shallow for usability; avoid deep nesting

## Frontend Integration

Navigation data is automatically shared to all Inertia pages via the `NavigationServiceProvider`:

```php
protected function shareInertia(): void
{
    Inertia::share([
        'navigation' => fn() => $this->app->make(NavigationManager::class)->all(),
    ]);
}
```

### Vue Component Usage

The `DashboardSidebar.vue` component renders the navigation in the dashboard layout:

```vue
<template>
    <UDashboardSidebar collapsible resizable>
        <template #default="{ collapsed }">
            <UNavigationMenu
                :collapsed="collapsed"
                :items="navigation"
                orientation="vertical"
            />
        </template>
    </UDashboardSidebar>
</template>

<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const navigation = computed(() => page.props.navigation);
</script>
```

The component:
- Receives navigation from `page.props.navigation` (shared by Inertia)
- Passes items to `UNavigationMenu` component for rendering
- Supports collapsible/resizable sidebar layout
- Provides extension points (e.g., `navigation.sidebar.footer`) for module customization

## Architecture Notes

- **Pure Service Layer**: No routes, models, or migrations. Navigation is purely a data service.
- **Read-Only After Registration**: Items are registered during service provider boot; there is no modification or deletion API.
- **Lazy Evaluation**: Badge callables are invoked per-request, ensuring real-time values.
- **Type Safe**: Uses PHP readonly properties and type hints for safety.
- **Module Independence**: Each module registers its own items independently via dependency injection.
