# Module Communication

Modules in this CMS are self-contained and should not directly depend on each other. Instead, they communicate through
two mechanisms: **events** and **ModuleHelper**.

## ModuleHelper

`ModuleHelper` allows a module to execute code conditionally based on whether another module is currently loaded. This
is the primary way to create optional integrations between modules without hard dependencies.

```php
use App\Core\Module\ModuleHelper;

// Execute code only if the Logger module is loaded
ModuleHelper::when('Logger', function () {
    CmsLog::info('MyModule', 'my.action', 'Something happened.');
});

// Execute code only if a module is NOT loaded
ModuleHelper::unless('Permissions', function () {
    // Fallback behavior when Permissions is unavailable
});

// Check manually
if (ModuleHelper::has('PageBuilder')) {
    // ...
}
```

### Available methods

| Method                                                     | Description                                          |
|------------------------------------------------------------|------------------------------------------------------|
| `ModuleHelper::when(string $module, callable $callback)`   | Executes `$callback` if the module is loaded         |
| `ModuleHelper::unless(string $module, callable $callback)` | Executes `$callback` if the module is **not** loaded |
| `ModuleHelper::has(string $module): bool`                  | Returns `true` if the module is loaded               |

::: tip
The module name is case-sensitive and must match the `name` field in the module's `module.json`.
:::

## Events

Laravel events are the standard way for modules to react to actions performed by other modules. The emitting module
dispatches an event, and any other module can listen to it without the emitter knowing about the listener.

### Dispatching an event

```php
use App\Core\Auth\Events\UserCreated;

UserCreated::dispatch($user, $payload);
```

### Listening to an event

Register your listener inside your module's `ServiceProvider::boot()`, ideally wrapped in a `ModuleHelper::when()` to
avoid errors if the emitting module is not loaded:

```php
use App\Core\Auth\Events\UserCreated;
use App\Core\Module\ModuleHelper;

public function boot(): void
{
    parent::boot();

    ModuleHelper::when('Auth', function () {
        \Event::listen(UserCreated::class, function (UserCreated $event) {
            // React to user creation
        });
    });
}
```

### Available events

| Event                                     | Module      | Dispatched when                    |
|-------------------------------------------|-------------|------------------------------------|
| `App\Core\Auth\Events\UserCreated`        | Auth        | A user is created via the web form |
| `App\Core\Auth\Events\UserEdited`         | Auth        | A user is updated via the web form |
| `App\Core\Permissions\Events\RoleCreated` | Permissions | A role is created                  |
| `App\Core\Permissions\Events\RoleUpdated` | Permissions | A role is updated                  |

## Real-world examples

### Logger integration

The Logger module is purely optional. Any module that wants to log activity wraps its logging calls in
`ModuleHelper::when('Logger')`:

```php
use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;

ModuleHelper::when('Logger', function () use ($user) {
    CmsLog::info(
        category: 'Auth',
        action: 'user.created',
        message: "User '{$user->name}' successfully created.",
        context: ['user' => $user->toArray()],
        subject: $user
    );
});
```

This means the Logger module can be disabled at any time without breaking any other module.

### Permissions + Auth integration

The Permissions module listens to Auth events to sync roles and permissions when a user is created or edited:

```php
// In PermissionServiceProvider::boot()
ModuleHelper::when('Auth', function () {
    \Event::listen(UserCreated::class, function (UserCreated $event) {
        if (auth()->user()->can('role_assign')) {
            $event->user->roles()->sync($event->payload['roles']);
        }

        if (auth()->user()->can('permission_assign')) {
            $ids = Permission::whereIn('name', $event->payload['permissions'])->pluck('id')->all();
            $event->user->permissions()->sync($ids);
        }
    });
});
```

### Auth + Permissions integration

The Auth module listens to Permissions events to sync users assigned to a role:

```php
// In AuthServiceProvider::boot()
ModuleHelper::when('Permissions', function () {
    \Event::listen(RoleCreated::class, function (RoleCreated $event) {
        if (auth()->user()->can('role_assign')) {
            $event->role->users()->sync($event->payload['users']);
        }
    });
});
```

## Frontend module communication

On the frontend, modules communicate through two shared mechanisms.

### ExtensionRegistry

A module can inject Vue components into another module's pages without any direct dependency, using extension points:

```ts
// app/Modules/MyModule/Resources/js/extensions.ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import MyComponent from './Components/MyComponent.vue';

ExtensionRegistry.register('users.create.end', MyComponent);
```

See [Extension Points](/modules/extension-points) for the full list of available points.

### Inertia shared props

Modules can share data globally via `Inertia::share()` in their service provider, making it available on every page via
`usePage().props`:

```php
// In MyModuleServiceProvider::boot()
Inertia::share([
    'myData' => fn() => MyModel::all(),
]);
```

```ts
import {usePage} from '@inertiajs/vue3';

const page = usePage();
const myData = page.props.myData;
```

::: warning
Be careful with `Inertia::share()` — data shared here is included in every response. Prefer `Inertia::optional()` for
data that is only needed on specific pages.

```php
Inertia::share([
    'myData' => Inertia::optional(fn() => MyModel::all()),
]);
```

:::
