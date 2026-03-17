# Events

All events dispatched across core and optional modules. Events are the standard way for modules to communicate without
hard dependencies.

## Auth module

### `UserCreated`

Dispatched after a user is successfully created via the web form.

```php
use App\Core\Auth\Events\UserCreated;

public User $user;
public array $payload;
```

| Property   | Type    | Description                                        |
|------------|---------|----------------------------------------------------|
| `$user`    | `User`  | The newly created user model                       |
| `$payload` | `array` | Full validated request data including `extensions` |

**Listening:**

```php
\Event::listen(UserCreated::class, function (UserCreated $event) {
    // $event->user
    // $event->payload['extensions']
});
```

### `UserEdited`

Dispatched after a user is successfully updated via the web form.

```php
use App\Core\Auth\Events\UserEdited;

public User $user;
public array $payload;
```

| Property   | Type    | Description                                        |
|------------|---------|----------------------------------------------------|
| `$user`    | `User`  | The updated user model                             |
| `$payload` | `array` | Full validated request data including `extensions` |

## Permissions module

### `RoleCreated`

Dispatched after a role is successfully created.

```php
use App\Core\Permissions\Events\RoleCreated;

public Role $role;
public array $payload;
```

| Property   | Type    | Description                                        |
|------------|---------|----------------------------------------------------|
| `$role`    | `Role`  | The newly created role model                       |
| `$payload` | `array` | Full validated request data including `extensions` |

### `RoleUpdated`

Dispatched after a role is successfully updated.

```php
use App\Core\Permissions\Events\RoleUpdated;

public Role $role;
public array $payload;
```

| Property   | Type    | Description                                        |
|------------|---------|----------------------------------------------------|
| `$role`    | `Role`  | The updated role model                             |
| `$payload` | `array` | Full validated request data including `extensions` |

## Who listens to what

| Event         | Listener                    | Module      | Action                                                       |
|---------------|-----------------------------|-------------|--------------------------------------------------------------|
| `UserCreated` | `PermissionServiceProvider` | Permissions | Syncs roles and direct permissions from `extensions` payload |
| `UserEdited`  | `PermissionServiceProvider` | Permissions | Syncs roles and direct permissions from `extensions` payload |
| `RoleCreated` | `AuthServiceProvider`       | Auth        | Syncs users assigned to the role from `extensions` payload   |
| `RoleUpdated` | `AuthServiceProvider`       | Auth        | Syncs users assigned to the role from `extensions` payload   |

## Best practices

Always wrap your event listeners in `ModuleHelper::when()` to avoid errors if the emitting module is not loaded:

```php
use App\Core\Module\ModuleHelper;
use App\Core\Auth\Events\UserCreated;

public function boot(): void
{
    parent::boot();

    ModuleHelper::when('Auth', function () {
        \Event::listen(UserCreated::class, function (UserCreated $event) {
            // your logic
        });
    });
}
```
