# BaseModuleServiceProvider

Abstract base class that all module service providers must extend. Handles automatic loading of all module resources and
provides hooks for permissions and navigation registration.

## Usage

```php
<?php

namespace App\Modules\Blog\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class BlogServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Blog';

    protected array $permissions = [
        'post_create' => [
            'name'        => 'Create blog posts',
            'description' => 'Ability to create new blog posts',
        ],
    ];

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
}
```

## Properties

### `$name`

- **Type:** `string`
- **Default:** `CoreModule`
- **Required:** yes

The name of the module. Must match the `name` field in `module.json`. Used as the namespace for translations, the key
for permission registration, and the identifier for `ModuleHelper::when()`.

```php
protected string $name = 'Blog';
```

### `$permissions`

- **Type:** `array`
- **Default:** `[]`

Permissions to register with the `PermissionRegistry` when the module boots. Each entry is a permission key mapped to a
`name` and `description`.

```php
protected array $permissions = [
    'post_create' => [
        'name'        => 'Create blog posts',
        'description' => 'Ability to create new blog posts',
    ],
];
```

## Methods

### `getNavigations()`

- **Return type:** `array`
- **Default:** `[]`

Override this method to register navigation items in the sidebar. Items are registered after all providers are booted.

```php
public function getNavigations(): array
{
    return [
        [
            'label'    => 'Blog',
            'icon'     => 'i-lucide-book-open',
            'children' => [
                ['label' => 'All posts',   'route' => 'blog.index'],
                ['label' => 'Create post', 'route' => 'blog.create'],
            ],
        ],
    ];
}
```

See [NavigationManager](/api/navigation-manager) for the full list of available item properties.

### `register()`

Called before `boot()`. Use it to bind services into the container. Always call `parent::register()` first — it includes
a guard to prevent double registration.

```php
public function register(): void
{
    parent::register();

    $this->app->singleton(MyService::class, fn() => new MyService());
}
```

### `boot()`

Called after all providers are registered. Always call `parent::boot()` first — it triggers all auto-loading logic.

```php
public function boot(): void
{
    parent::boot();

    // Your boot logic here
}
```

## Auto-loaded resources

All of the following are loaded automatically by `parent::boot()` with no manual registration needed.

| Method                   | Path                   | Behavior                                                      |
|--------------------------|------------------------|---------------------------------------------------------------|
| `registerRoutes()`       | `Routes/web.php`       | Loaded with `web` middleware                                  |
| `registerRoutes()`       | `Routes/api.php`       | Loaded with `web` + `auth`, prefixed `/api`, named `api.*`    |
| `registerMigrations()`   | `Migrations/`          | Registered with `loadMigrationsFrom()`                        |
| `registerCommands()`     | `Console/Commands/`    | Auto-discovered, console mode only                            |
| `registerSchedule()`     | `Console/schedule.php` | Required after all providers are booted, console mode only    |
| `registerConfig()`       | `Config/*.php`         | Merged with `mergeConfigFrom()`, key = filename               |
| `registerTranslations()` | `Resources/lang/`      | Loaded with module name as namespace                          |
| `registerNavigations()`  | —                      | Calls `getNavigations()` after all providers are booted       |
| `registerPermissions()`  | —                      | Registers `$permissions` into `PermissionRegistry` after boot |

## Double registration guard

`BaseModuleServiceProvider` includes static guards on both `register()` and `boot()` to prevent a provider from being
registered or booted more than once, even if the application attempts to load it multiple times.

```php
protected static array $registered = [];
protected static array $booted = [];
```

This ensures that modules loaded both as core and from the database do not cause conflicts.
