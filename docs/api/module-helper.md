# ModuleHelper

Static utility class for conditional module integrations. Allows modules to execute code only when another module is
loaded, without creating hard dependencies.

## Usage

```php
use App\Core\Module\ModuleHelper;

ModuleHelper::when('Logger', function () {
    CmsLog::info('MyModule', 'my.action', 'Something happened.');
});
```

## Methods

### `when()`

Executes a callback if the specified module is currently loaded.

```php
ModuleHelper::when(string $moduleName, callable $callback): void
```

```php
ModuleHelper::when('Logger', function () use ($user) {
    CmsLog::info('Auth', 'user.created', "User '{$user->name}' created.", [], $user);
});
```

### `unless()`

Executes a callback if the specified module is **not** loaded.

```php
ModuleHelper::unless(string $moduleName, callable $callback): void
```

```php
ModuleHelper::unless('Permissions', function () {
    // Fallback when Permissions module is disabled
});
```

### `has()`

Returns `true` if the specified module is currently loaded.

```php
ModuleHelper::has(string $moduleName): bool
```

```php
if (ModuleHelper::has('PageBuilder')) {
    $registry = app(BlockRegistry::class);
    $registry->register(MyBlock::class);
}
```

## Notes

- Module names are **case-sensitive** and must match the `name` field in the module's `module.json`
- `ModuleHelper` resolves the `ModuleManager` from the service container on every call
- It only reflects the state of modules at the time of the call — a module toggled at runtime will be reflected
  immediately
