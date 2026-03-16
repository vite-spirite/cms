# BaseModuleServiceProvider

The base class for all module service providers.

## Usage

```php
<?php

namespace App\Modules\YourModule\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class YourModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'YourModule';
    
    protected array $permissions = [];
    
    public function getNavigations() : array {
        return [];
    }
}
```

## Properties

### `$name`

- **Type:** `string`
- **Required:** Yes
- **Description:** The display name of the module

### `$permissions`

- **Type:** `array`
- **Description:** Permissions to register

**Example:**

```php
protected array $permissions = [
    'module.action' => [
        'name' => 'Permission name displayed in ACP',
        'description' => 'Permission description displayed in ACP'
    ],
];
```

### `getNavigations`

- **Return type:** `array`
- **Description:** Navigation items to register

**Example:**

```php
public function getNavigations(): array
{
    return [
        'label' => 'Dashboard',
        'icon' => 'i-lucide-home',
        'route' => 'route.name',
    ];
}
```

## Auto-Loaded Resources

The following resources are automatically loaded:

- **Routes:** `Routes/web.php`, `Routes/api.php`
- **Migrations:** `Migrations/*.php`
- **Commands:** `Console/Commands/*.php`
- **Config:** `Config/*.php`
- **Views:** `Resources/views/*`
- **Translations:** `Resources/lang/*`

## Lifecycle Hooks

### `register()`

Called when the service provider is registered.

### `boot()`

Called when the service provider is booted.

### `booted()`

Called after all providers are booted (use for optional integrations).
