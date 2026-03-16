# Core Layer

The **Core** layer is the foundation of this CMS, providing essential infrastructure that all other modules depend on. It's built with Laravel 12, Vue 3, TypeScript, Inertia.js, and TailwindCSS.

The Core layer contains four key modules that work together to provide authentication, permissions, navigation, and an extensible module system:

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + TypeScript
- **Full-Stack Bridge**: Inertia.js
- **Styling**: TailwindCSS
- **State Management**: Inertia props + composables

## Core Modules

| Module | Purpose | README |
|--------|---------|--------|
| **Module** | Dynamic module discovery, registration, and lifecycle management. The foundation that enables all functionality as loadable modules. | [Module/README.md](./Module/README.md) |
| **Auth** | User authentication, login/logout, and user management. Handles admin access and user CRUD with event hooks. | [Auth/README.md](./Auth/README.md) |
| **Navigation** | Centralized sidebar navigation registry. Modules register menu items dynamically; backend shares with frontend via Inertia. | [Navigation/README.md](./Navigation/README.md) |
| **Permissions** | Role-Based Access Control (RBAC). Manages roles, permissions, and user assignments with owner/superadmin support. | [Permissions/README.md](./Permissions/README.md) |

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Vue 3 + TS)                    │
│  useGate • useApi • ExtensionRegistry • Navigation Sidebar  │
└─────────────────────────────────────────────────────────────┘
                           ↑
                    Inertia Props (Shared Data)
                           ↑
┌─────────────────────────────────────────────────────────────┐
│              Laravel Middleware + Controllers               │
│    Auth Routes  •  Permission Checks  •  Inertia Responses │
└─────────────────────────────────────────────────────────────┘
                           ↑
                    HTTP Request/Response
                           ↑
┌─────────────────────────────────────────────────────────────┐
│                   Core Layer Services                       │
├──────────────┬───────────────┬──────────────┬───────────────┤
│  ModuleManager │ PermissionRegistry │ NavigationManager  │
│  (Discovery,   │ (RBAC system)      │ (Menu items)       │
│   Loading)     │                    │                    │
└──────────────┴───────────────┴──────────────┴───────────────┘
                           ↑
┌─────────────────────────────────────────────────────────────┐
│                   Database (Eloquent Models)                │
│  modules • users • roles • permissions • user_role • ...    │
└─────────────────────────────────────────────────────────────┘
```

## Bootstrap Order

The CMS initializes modules and core systems in this sequence:

1. **ModuleManager** is registered as a service provider
2. **discovers()** scans `app/Core` and `app/Modules` for `module.json` files
3. **loadStoredModules()** is called during bootstrap, restoring modules from the database
4. **Core modules** (Auth, Navigation, Permissions) are loaded
5. **Module ServiceProviders** register and boot:
   - Routes (web.php + api.php)
   - Migrations
   - CLI commands
   - Scheduled tasks
   - Config files
   - Translations
   - Navigation items
   - Permissions
6. **Inertia data** is shared (navigation, permissions, users)
7. **Application is ready** — all loaded modules are active

## Creating a New Module

To create a new module in the Core layer:

### 1. Directory Structure

Create a directory in `app/Core/MyModule/`:

```
app/Core/MyModule/
├── module.json
├── Providers/
│   └── MyModuleServiceProvider.php
├── Routes/
│   ├── web.php
│   └── api.php
├── Migrations/
├── Console/
│   ├── Commands/
│   │   └── MyCommand.php
│   └── schedule.php
├── Config/
│   └── mymodule.php
├── Resources/
│   ├── lang/
│   └── js/
├── Models/
├── Controllers/
├── Requests/
├── Events/
└── Services/
```

### 2. module.json

Define module metadata:

```json
{
  "name": "MyModule",
  "type": "Core",
  "provider": "App\\Core\\MyModule\\Providers\\MyModuleServiceProvider",
  "description": "A description of what this module does"
}
```

- **name**: Unique module identifier (used by ModuleManager)
- **type**: `"Core"` or `"Plugin"` (determines load priority)
- **provider**: Full class name of the ServiceProvider
- **description**: Brief module purpose

### 3. Service Provider

Create `Providers/MyModuleServiceProvider.php` extending `BaseModuleServiceProvider`:

```php
namespace App\Core\MyModule\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class MyModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'MyModule';

    protected array $permissions = [
        'mymodule.create' => [
            'name' => 'Create',
            'description' => 'Create items',
        ],
        'mymodule.edit' => [
            'name' => 'Edit',
            'description' => 'Edit items',
        ],
        'mymodule.delete' => [
            'name' => 'Delete',
            'description' => 'Delete items',
        ],
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'My Module',
                'icon' => 'i-lucide-icon-name',
                'route' => 'mymodule.index',
                'badge' => fn() => \App\Core\MyModule\Models\Item::count(),
            ],
        ];
    }
}
```

The base class automatically registers:
- Routes from `Routes/web.php` and `Routes/api.php`
- Migrations from `Migrations/`
- CLI commands from `Console/Commands/`
- Config files from `Config/`
- Translations from `Resources/lang/`
- Navigation items via `getNavigations()`
- Permissions via the `$permissions` property

### 4. Register Routes

Create `Routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Core\MyModule\Controllers\ItemController;

Route::middleware('auth')->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('mymodule.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('mymodule.create');
    Route::post('/items', [ItemController::class, 'store'])->name('mymodule.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('mymodule.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('mymodule.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('mymodule.destroy');
});
```

Create `Routes/api.php` for API endpoints:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Core\MyModule\Controllers\ItemApiController;

Route::prefix('items')->group(function () {
    Route::get('/', [ItemApiController::class, 'index']);
    Route::get('/{item}', [ItemApiController::class, 'show']);
    Route::post('/', [ItemApiController::class, 'store']);
});
```

API routes are automatically prefixed with `/api` and protected with `auth` middleware.

### 5. Define Permissions

Set the `$permissions` array in the ServiceProvider. Each permission maps to a display name and description:

```php
protected array $permissions = [
    'mymodule.create' => [
        'name' => 'Create items',
        'description' => 'Allow users to create new items',
    ],
    'mymodule.edit' => [
        'name' => 'Edit items',
        'description' => 'Allow users to edit existing items',
    ],
];
```

These are synced to the database automatically and can be assigned to roles via the Permissions module.

### 6. Register Navigation Items

Override `getNavigations()` to register menu items:

```php
public function getNavigations(): array
{
    return [
        [
            'label' => 'My Module',
            'icon' => 'i-lucide-icon-name',
            'route' => 'mymodule.index',
            'badge' => fn() => \App\Core\MyModule\Models\Item::count(),
            'children' => [
                [
                    'label' => 'Create Item',
                    'route' => 'mymodule.create',
                ],
            ],
        ],
    ];
}
```

Navigation items are shared to the frontend via Inertia props and rendered in the sidebar.

## Key Frontend Patterns

### ExtensionRegistry

A TypeScript registry for registering components at extension points:

```typescript
import ExtensionRegistry from '@/Core/Module/ExtensionRegistry';

// Register a block type
ExtensionRegistry.register('block.types', MyBlockComponent);

// Resolve all components at an extension point
const blockTypes = ExtensionRegistry.resolve('block.types');
```

Common extension points: `block.toolbar`, `block.types`, `menu.items`, `settings.sections`

### useApi Composable

Fetch data from your API with type safety:

```typescript
import { useApi } from '@/Core/Module/Composables/useApi';

const { get } = useApi();

interface Post {
    id: number;
    title: string;
}

const post = await get<Post>('/api/posts/1');
// post is typed as Post
```

Automatically includes proper headers and error handling.

### useGate Composable

Check permissions in Vue components:

```typescript
import { useGate } from '@/Core/Module/Composables/useGate';

const { can, canAny, isOwner } = useGate();

if (can('items.create')) {
    // Show create button
}

if (canAny(['items.edit', 'items.delete'])) {
    // Show edit/delete menu
}

if (isOwner.value) {
    // Show admin panel
}
```

Permissions are passed from the backend via Inertia props.

## Integration Points

### ModuleHelper (Backend)

Check module availability throughout your application:

```php
use App\Core\Module\ModuleHelper;

if (ModuleHelper::has('Auth')) {
    // Auth module is active
}

ModuleHelper::when('Gallery', function () {
    // Register gallery-specific features
});
```

### Listening to Events

Modules dispatch events for lifecycle hooks. Listen in your service provider:

```php
use App\Core\Auth\Events\UserCreated;
use App\Core\Permissions\Events\RoleCreated;

\Event::listen(UserCreated::class, function (UserCreated $event) {
    // $event->user, $event->payload
});

\Event::listen(RoleCreated::class, function (RoleCreated $event) {
    // $event->role, $event->payload
});
```

### Permission Checking (Backend)

Use Laravel Gates to protect controller actions:

```php
use Illuminate\Support\Facades\Gate;

Route::post('/items', [ItemController::class, 'store'])
    ->middleware('can:mymodule.create');

// In a controller
public function store(Request $request)
{
    $this->authorize('mymodule.create');
    // Create item
}

// In a view or service
if (Gate::allows('mymodule.edit')) {
    // Show edit option
}
```

### Permission Checking (Frontend)

Use the `useGate` composable in Vue templates:

```vue
<template>
    <button v-if="can('mymodule.create')">Create Item</button>
    <button v-if="can('mymodule.edit')">Edit Item</button>
    <div v-if="isOwner">Admin Only</div>
</template>

<script setup lang="ts">
import { useGate } from '@/Core/Module/Composables/useGate';

const { can, isOwner } = useGate();
</script>
```

## Database Setup

Run migrations to set up the Core layer:

```bash
php artisan migrate
```

This creates:
- `modules` — tracks module load state
- `users` — user accounts
- `roles` — role definitions
- `permissions` — permission registry
- `role_permission` — role-permission mappings
- `user_role` — user-role assignments
- `user_permissions` — direct user permissions

## Common Tasks

### Load a Module at Runtime

```php
use App\Core\Module\ModuleManager;

$manager = app(ModuleManager::class);
$manager->loadModule('MyModule');
```

### Register a Permission Programmatically

```php
use App\Core\Permissions\Service\PermissionRegistry;

$registry = app(PermissionRegistry::class);
$registry->register('mymodule', 'items.create', 'Create Items', 'Allow item creation');
$registry->sync(); // Persist to database
```

### Register a Navigation Item Programmatically

```php
use App\Core\Navigation\Service\NavigationManager;

$nav = app(NavigationManager::class);
$nav->register([
    'label' => 'My Item',
    'route' => 'my.route',
    'icon' => 'i-lucide-icon',
]);
```

### Make a User an Owner (Superadmin)

```bash
php artisan permissions:owner make user@example.com
php artisan permissions:owner revoke user@example.com
php artisan permissions:owner list
```

Owners bypass all permission checks.

## Further Reading

- [Module System Details](./Module/README.md)
- [Auth Module & User Management](./Auth/README.md)
- [Navigation & Sidebar Management](./Navigation/README.md)
- [Permissions & RBAC](./Permissions/README.md)
