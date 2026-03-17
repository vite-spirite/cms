# PermissionRegistry

Singleton service that collects all permissions declared by modules and syncs them to the database. It is the source of
truth for all available permissions in the application.

## Usage

```php
$registry = app(\App\Core\Permissions\Service\PermissionRegistry::class);
```

## Methods

### `register()`

Registers a single permission.

```php
register(string $module, string $permission, string $name, string $description): void
```

```php
$registry->register('Blog', 'post_create', 'Create blog posts', 'Ability to create new blog posts');
```

### `registerMany()`

Registers multiple permissions at once. This is what `BaseModuleServiceProvider` calls internally with the
`$permissions` property.

```php
registerMany(string $module, array $permissions): void
```

```php
$registry->registerMany('Blog', [
    'post_create' => [
        'name'        => 'Create blog posts',
        'description' => 'Ability to create new blog posts',
    ],
    'post_delete' => [
        'name'        => 'Delete blog posts',
        'description' => 'Ability to delete blog posts',
    ],
]);
```

### `all()`

Returns all registered permissions as a flat array keyed by permission name.

```php
all(): array
```

```php
[
    'post_create' => [
        'module'       => 'Blog',
        'name'         => 'post_create',
        'display_name' => 'Create blog posts',
        'description'  => 'Ability to create new blog posts',
    ],
    // ...
]
```

### `groupByModule()`

Returns all registered permissions grouped by module name. Used by the permissions assignment UI and the API.

```php
groupByModule(): array
```

```php
[
    'Blog' => [
        ['name' => 'post_create', 'display_name' => 'Create blog posts', ...],
        ['name' => 'post_delete', 'display_name' => 'Delete blog posts', ...],
    ],
    'Auth' => [
        // ...
    ],
]
```

### `sync()`

Upserts all registered permissions into the `permissions` database table. Must be called before assigning permissions to
users or roles to ensure the database is up to date.

```php
sync(): void
```

```php
$registry->sync();

$ids = Permission::whereIn('name', $permissionNames)->pluck('id')->all();
$user->permissions()->sync($ids);
```

::: warning
`sync()` is not called automatically on every request for performance reasons. It is called explicitly before any
permission assignment operation (role create/update, user create/update).
:::

## Gates

The `PermissionServiceProvider` automatically defines a Laravel Gate for every permission registered in the registry at
boot time:

```php
Gate::define('post_create', function ($user) {
    return $user->hasPermission('post_create');
});
```

This means you can use any registered permission key directly with `Gate::allows()`, `can()`, or the `can:` middleware
without any additional setup.
