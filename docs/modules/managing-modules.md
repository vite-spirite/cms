# Managing Modules

Modules can be enabled and disabled directly from the admin interface without touching any code.

## Module manager UI

Navigate to the admin dashboard home (`/admin`) to access the module manager. It displays all discovered modules with a
toggle switch for each one.

- **Core modules** (`type: core`) are always loaded and their toggle is disabled
- **Optional modules** (`type: module`) can be freely enabled or disabled

::: warning
The module manager requires npm to be available on the server to rebuild the frontend after toggling a module. If npm is
not found, a warning banner is displayed at the top of the page.
:::

## Enabling a module

When you enable a module:

1. The module is marked as `loaded: true` in the `modules` database table
2. The service provider is registered immediately
3. A `RebuildFrontendJob` is dispatched to rebuild the frontend assets
4. The page reloads via `Inertia::location()` once the job completes

::: tip
After enabling a module for the first time, run `php artisan migrate` to apply its migrations before using it.
:::

## Disabling a module

When you disable a module:

1. The module is marked as `loaded: false` in the `modules` database table
2. The following caches are cleared automatically:
    - `php artisan cache:clear`
    - `php artisan route:clear`
    - `php artisan view:clear`
3. A `RebuildFrontendJob` is dispatched to rebuild the frontend assets
4. The page reloads via `Inertia::location()`

::: warning
Disabling a module does **not** unregister its service provider for the current request. A full application restart is
required for the module to be completely unloaded from memory. On subsequent requests it will no longer be loaded.
:::

## Octane support

If your application runs with Laravel Octane, the module manager will automatically reload the Octane server after
disabling a module:

```php
if (config('octane.server')) {
    \Artisan::call('octane:reload');
}
```

## Module discovery

Modules are discovered automatically at boot time by scanning two directories:

- `app/Core/` — core modules
- `app/Modules/` — optional modules

Any subdirectory containing a `module.json` file is registered as an available module. Discovery runs every time the
application boots so new modules are picked up without any manual registration.

::: tip
If a module is not appearing in the module manager, check that its `module.json` is valid and that the `provider` class
exists and is autoloaded by Composer.
:::

## Database table

The `modules` table tracks the loaded state of optional modules:

| Column      | Type      | Description                                  |
|-------------|-----------|----------------------------------------------|
| `id`        | integer   | Primary key                                  |
| `name`      | string    | Module name, matches `name` in `module.json` |
| `loaded`    | boolean   | Whether the module is currently enabled      |
| `loaded_at` | timestamp | Last time the module was toggled on          |

Core modules are never stored in this table as they are always loaded regardless.

## Required permission

Toggling modules requires the `module_manage` permission. Users without this permission will not see the toggle switches
in the UI.
