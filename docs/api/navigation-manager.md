# NavigationManager

Singleton service that collects and resolves sidebar navigation items. Each module registers its items via
`getNavigations()` in its service provider.

## Usage

The `NavigationManager` is bound as a singleton in the container and should not be instantiated directly.

```php
$navigationManager = app(\App\Core\Navigation\Service\NavigationManager::class);
```

## Methods

### `register()`

Registers a single navigation item.

```php
register(array|NavigationItem $item): void
```

```php
$navigationManager->register([
    'label' => 'Blog',
    'icon'  => 'i-lucide-book-open',
    'route' => 'blog.index',
]);
```

### `registerMany()`

Registers multiple navigation items at once. This is what `BaseModuleServiceProvider` calls internally with the return
value of `getNavigations()`.

```php
registerMany(array $items): void
```

```php
$navigationManager->registerMany([
    [
        'label' => 'Blog',
        'icon'  => 'i-lucide-book-open',
        'route' => 'blog.index',
    ],
    [
        'label'    => 'Settings',
        'icon'     => 'i-lucide-settings',
        'children' => [
            ['label' => 'General', 'route' => 'settings.general'],
            ['label' => 'Advanced', 'route' => 'settings.advanced'],
        ],
    ],
]);
```

### `all()`

Returns all registered navigation items as an array, ready to be shared via Inertia.

```php
all(): array
```

## NavigationItem properties

| Property   | Type     | Required | Description                                         |
|------------|----------|----------|-----------------------------------------------------|
| `label`    | `string` | yes      | Text displayed in the sidebar                       |
| `icon`     | `string` | no       | Iconify icon name (e.g. `i-lucide-home`)            |
| `route`    | `string` | no       | Laravel route name, resolved to a URL automatically |
| `badge`    | `mixed`  | no       | Badge value, accepts a callable for dynamic values  |
| `disabled` | `bool`   | no       | Disables the item, defaults to `false`              |
| `children` | `array`  | no       | Nested `NavigationItem` arrays                      |
| `class`    | `string` | no       | Additional CSS classes                              |

### Dynamic badge

The `badge` property accepts a callable that is evaluated at render time on each request:

```php
[
    'label' => 'Posts',
    'icon'  => 'i-lucide-file-text',
    'route' => 'blog.index',
    'badge' => fn() => Post::where('status', 'draft')->count(),
]
```

### Route resolution

The `route` property is automatically resolved to a relative URL using `URL::route($route, [], false)`. You only need to
provide the route name, not the full URL.

## Shared via Inertia

The full navigation tree is shared on every page as `page.props.navigation` by the `NavigationServiceProvider`:

```php
Inertia::share([
    'navigation' => fn() => $this->app->make(NavigationManager::class)->all(),
]);
```

It is consumed by the `DashboardSidebar` component and passed directly to `UNavigationMenu`.
