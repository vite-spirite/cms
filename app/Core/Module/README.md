# Module System

The Module System is the foundation of this CMS, providing dynamic module discovery, registration, and lifecycle management. All functionality—whether core or extended—is built as modules that can be loaded, unloaded, and managed at runtime.

## Overview

The module system enables:
- **Dynamic discovery** of modules from the `Core` and `Modules` directories
- **Runtime loading/unloading** of modules with persistent state tracking
- **Automatic registration** of routes, migrations, commands, schedules, config, translations, permissions, and navigation items
- **TypeScript extension points** for frontend features
- **Composable utilities** for permission checks and API calls

## Architecture

### ModuleManager

The `ModuleManager` is the central orchestrator for module discovery and lifecycle.

**Public Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `discovers()` | `void` | Scan `Core` and `Modules` directories for `module.json` files and populate available modules |
| `getActiveModules()` | `array` | Get names of currently loaded modules |
| `getAvailableModules()` | `array` | Get all discovered modules (loaded or not) |
| `getModule(moduleName)` | `array\|null` | Get metadata for a specific module |
| `isModuleLoaded(moduleName)` | `bool` | Check if a module is currently active |
| `loadModules(type)` | `void` | Load all modules matching a given type (e.g., `'Core'`, `'Plugin'`) |
| `loadModule(moduleName)` | `bool` | Load a single module by name; records load time and persists to database |
| `unloadModule(moduleName)` | `bool` | Unload a module by name; updates database |
| `loadStoredModules()` | `void` | Load all modules marked as loaded in the database (called during boot) |

**Usage:**

```php
$manager = app(ModuleManager::class);

// Check available modules
$modules = $manager->getAvailableModules();

// Load a module at runtime
$manager->loadModule('Auth');

// Check if loaded
if ($manager->isModuleLoaded('Auth')) {
    // Auth features are available
}

// Unload a module
$manager->unloadModule('Auth');
```

### Module Discovery

Modules are discovered by scanning directories for `module.json` files. The structure should be:

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
└── ...
```

**module.json Example:**

```json
{
  "name": "MyModule",
  "type": "Core",
  "provider": "App\\Core\\MyModule\\Providers\\MyModuleServiceProvider",
  "description": "Description of my module"
}
```

Module metadata is stored in `module.json` and indexed by the `ModuleManager` during discovery.

## BaseModuleServiceProvider

Each module provides a `ServiceProvider` that extends `BaseModuleServiceProvider`. This base class automatically registers and bootstraps module resources.

**Auto-Registered Components:**

1. **Routes** – Registers `Routes/web.php` and `Routes/api.php` if they exist
   - Web routes: middleware `web`
   - API routes: middleware `web, auth` with prefix `api` and name prefix `api.`

2. **Migrations** – Loads all migrations from `Migrations/` directory

3. **Commands** – Auto-discovers and registers CLI commands from `Console/Commands/`
   - Only available in console environment

4. **Scheduled Tasks** – Loads `Console/schedule.php` during boot
   - Access the schedule via the closure: `require $schedulePath`

5. **Config** – Merges all files from `Config/` into config repository

6. **Translations** – Loads translations from `Resources/lang/` namespace

7. **Navigation Items** – Registers via `getNavigations()` method
   - Returns array of navigation definitions
   - Registered with `NavigationManager`

8. **Permissions** – Registers via `$permissions` property
   - Set `$permissions` array in provider
   - Registered with `PermissionRegistry`

**Example ServiceProvider:**

```php
namespace App\Core\MyModule\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class MyModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'MyModule';

    protected array $permissions = [
        'mymodule.create',
        'mymodule.edit',
        'mymodule.delete',
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'My Module',
                'route' => 'mymodule.index',
                'icon' => 'icon-name',
            ],
        ];
    }
}
```

**Override Points:**

- `register()` – Called during service provider registration
- `boot()` – Called after all service providers registered; orchestrates all auto-registration
- `getNavigations()` – Override to provide navigation items
- Extend protected methods to customize registration behavior

## ModuleHelper

Static utility for checking module availability throughout your application.

**Public Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `has(moduleName)` | `bool` | Check if a module is currently loaded |
| `when(moduleName, callback)` | `void` | Execute callback only if module is loaded |
| `unless(moduleName, callback)` | `void` | Execute callback only if module is not loaded |

**Usage Examples:**

```php
use App\Core\Module\ModuleHelper;

// Simple check
if (ModuleHelper::has('Auth')) {
    // Auth module is active
}

// Conditional logic
ModuleHelper::when('Gallery', function () {
    // Register gallery-specific routes or features
});

ModuleHelper::unless('Shop', function () {
    // Show a "Shop coming soon" message
});
```

These utilities are useful in route definitions, service providers, and view logic where certain features should only be available when their module is loaded.

## Module Eloquent Model

The `Module` model tracks persistent load state in the database.

**Table: `modules`**

| Column | Type | Description |
|--------|------|-------------|
| `id` | increments | Primary key |
| `name` | string | Module name (unique) |
| `loaded` | boolean | Whether the module is loaded |
| `loaded_at` | timestamp | When the module was last loaded |

**Model Usage:**

```php
use App\Core\Module\Models\Module;

// Get all loaded modules
$loaded = Module::where('loaded', true)->get();

// Mark a module as loaded
Module::updateOrCreate(
    ['name' => 'Auth'],
    ['loaded' => true, 'loaded_at' => now()]
);

// Fields are cast automatically
// - 'loaded' as boolean
// - 'loaded_at' as timestamp
```

The database is the source of truth for which modules should be loaded on next boot. The `ModuleManager::loadStoredModules()` method is called during application bootstrap to restore the previously loaded state.

## Frontend Extension Registry

`ExtensionRegistry` provides a TypeScript mechanism for registering and resolving frontend components at extension points.

**API:**

```typescript
import ExtensionRegistry from '@/Core/Module/ExtensionRegistry';

// Register a component at an extension point
ExtensionRegistry.register('block.toolbar', MyToolbarComponent);

// Resolve all components registered at a point
const toolbarComponents = ExtensionRegistry.resolve('block.toolbar');

// Clear all registrations (useful in tests)
ExtensionRegistry.reset();
```

**Usage Pattern:**

```typescript
// In a module's setup
import ExtensionRegistry from '@/Core/Module/ExtensionRegistry';
import MyGalleryBlock from './Blocks/GalleryBlock.vue';

// Register the block at the extension point
ExtensionRegistry.register('block.types', MyGalleryBlock);

// In a page editor or block manager
const blockComponents = ExtensionRegistry.resolve('block.types');
blockComponents.forEach(component => {
    // Use component
});
```

Extension points are arbitrary strings; modules define their own extension point names. Common patterns:

- `block.toolbar` – toolbar extensions for block editors
- `block.types` – custom block types
- `menu.items` – additional menu items
- `settings.sections` – configuration sections

## useApi Composable

`useApi()` provides a typed, error-handling wrapper for fetching JSON from your API.

**API:**

```typescript
export function useApi() {
    async function get<T = unknown>(path: string): Promise<T>
}
```

**Usage:**

```typescript
<script setup lang="ts">
import { useApi } from '@/Core/Module/Composables/useApi';

interface Post {
    id: number;
    title: string;
}

const { get } = useApi();

// Fetch with type safety
const post = await get<Post>('/api/posts/1');
// post is typed as Post

// Error handling
try {
    const data = await get('/api/missing');
} catch (error) {
    console.error('API error:', error.message);
}
</script>
```

**Behavior:**

- Automatically includes `Accept: application/json` and `X-Requested-With: XMLHttpRequest` headers
- Throws an error if the response status is not OK
- Parses and returns JSON response
- Supports generic type parameter for response typing

## useGate Composable

`useGate()` provides permission checking in Vue components using data from the current page context.

**API:**

```typescript
export function useGate() {
    function can(permission: string): boolean
    function canAny(permissions: string[]): boolean
}
```

**Usage:**

```typescript
<script setup lang="ts">
import { useGate } from '@/Core/Module/Composables/useGate';

const { can, canAny } = useGate();
</script>

<template>
  <div>
    <!-- Show only if user can edit posts -->
    <button v-if="can('posts.edit')">Edit</button>

    <!-- Show if user can perform any of these actions -->
    <menu v-if="canAny(['posts.create', 'posts.import'])">
      <button>Create</button>
      <button>Import</button>
    </menu>

    <!-- Always visible if user is owner -->
    <section v-if="can('admin')">Admin panel</section>
  </div>
</template>
```

**Permission Resolution:**

The composable reads permissions from `page.props.permissions` (Inertia prop). Permission check returns:
- `true` if user is an owner (admin)
- `true` if user has the requested capability
- `false` otherwise
- `true` if no permissions are set (unrestricted)

Permissions must be passed from the backend via Inertia props:

```php
// In a controller
return inertia('SomePage', [
    'permissions' => [
        'capabilities' => ['posts.edit', 'posts.delete'],
        'owner' => false,
    ],
]);
```

## Database Setup

The `modules` table must exist before the system can track persistent module state. Create a migration:

```php
Schema::create('modules', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->boolean('loaded')->default(false);
    $table->timestamp('loaded_at')->nullable();
});
```

## Bootstrap Flow

1. **Application starts** → `ModuleManager` is registered
2. **discoveries()** is called → scans `Core` and `Modules` for `module.json` files
3. **loadStoredModules()** is called → restores modules marked as loaded in database
4. **Module ServiceProviders** register and boot → auto-registering routes, migrations, config, etc.
5. **Application is ready** → all loaded modules are active

Module load/unload can happen at any time after bootstrap, updating both the in-memory active list and the database.
