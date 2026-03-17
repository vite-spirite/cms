# Auth Module

The Auth module handles authentication and user management. It is a **core module**, meaning it is always loaded and
cannot be disabled.

## Overview

- User login / logout
- User CRUD (create, edit, delete, list)
- Artisan command to create users from CLI
- Dispatches events when users are created or edited (consumed by other modules like Permissions)

## Routes

### Web

| Method | URI                          | Name                         | Middleware                |
|--------|------------------------------|------------------------------|---------------------------|
| GET    | `/admin/login`               | `login`                      | `guest`                   |
| POST   | `/admin/login`               | `admin.login.request`        | `guest`, `throttle:5,1`   |
| POST   | `/admin/logout`              | `admin.logout`               | `auth`                    |
| GET    | `/admin/users/list`          | `admin.users.index`          | `auth`                    |
| GET    | `/admin/users/create`        | `admin.users.create`         | `auth`, `can:user_create` |
| POST   | `/admin/users/create`        | `admin.users.create.request` | `auth`, `can:user_create` |
| GET    | `/admin/users/edit/{user}`   | `admin.users.edit`           | `auth`, `can:user_edit`   |
| PUT    | `/admin/users/edit`          | `admin.users.edit.request`   | `auth`, `can:user_edit`   |
| DELETE | `/admin/users/delete/{user}` | `admin.users.delete`         | `auth`, `can:user_delete` |

## Permissions

| Key           | Display Name    | Description                      |
|---------------|-----------------|----------------------------------|
| `user_create` | Create new user | Ability to create a new user     |
| `user_edit`   | Edit user       | Ability to edit an existing user |
| `user_delete` | Delete user     | Ability to delete a user         |
| `user_view`   | View user       | Ability to view user list        |

### Checking permissions

**PHP:**

```php
Gate::allows('user_create');

// Via middleware on routes
Route::get('/users/create', ...)->middleware('can:user_create');
```

**Vue:**

```ts
import {useGate} from '@modules/Module/Composables/useGate';

const gate = useGate();

if (gate.can('user_create')) {
    // show create button
}
```

## Events

### `UserCreated`

Dispatched after a user is successfully created via the web form.

```php
use App\Core\Auth\Events\UserCreated;

public User $user;
public array $payload; // validated form data including 'extensions'
```

**Example listener:**

```php
\Event::listen(UserCreated::class, function (UserCreated $event) {
    // $event->user    — the newly created User model
    // $event->payload — the full validated request data
});
```

### `UserEdited`

Dispatched after a user is successfully updated via the web form.

```php
use App\Core\Auth\Events\UserEdited;

public User $user;
public array $payload;
```

::: tip
These events are the main integration point for the **Permissions** module, which listens to them to sync roles and
direct permissions after user creation or edition.
:::

## Extension Points

| Point name           | Location                       | Props passed           |
|----------------------|--------------------------------|------------------------|
| `users.create.start` | Top of the create user form    | `v-model` (extensions) |
| `users.create.end`   | Bottom of the create user form | `v-model` (extensions) |
| `users.edit.start`   | Top of the edit user form      | `v-model`, `user`      |
| `users.edit.end`     | Bottom of the edit user form   | `v-model`, `user`      |

**Registering a component on an extension point:**

```ts
// app/Modules/YourModule/Resources/js/extensions.ts
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import MyComponent from './Components/MyComponent.vue';

ExtensionRegistry.register('users.create.end', MyComponent);
```

Your component receives the `extensions` object as a `v-model` and any additional props defined in `extension-props`.

```vue

<script lang="ts" setup>
    const extensionValues = defineModel<Record<string, unknown>>({required: true});

    // Add your own key to the shared extensions payload
    extensionValues.value.myData = {foo: 'bar'};
</script>
```

On the Laravel side, the `extensions` key is passed through `CreateUserRequest` / `EditUserRequest` as:

```php
'extensions' => 'sometimes|array'
```

It is then forwarded to the dispatched event (`UserCreated`, `UserEdited`), where other modules can read it.

## Artisan Command

```bash
# You will be prompted for a password
php artisan auth:create-user "John Doe" john@example.com

# Or pass the password directly (not recommended in production)
php artisan auth:create-user "John Doe" john@example.com --password=secret123
```

## Shared Inertia Data

The Auth module shares the full user list via Inertia as an **optional** prop:

```php
Inertia::share([
    'users' => Inertia::optional(fn() => User::all()),
]);
```

This means `users` is only loaded when explicitly requested using `router.reload({ only: ['users'] })`. It is used
internally by the `AssignRole` component from the Permissions module.

## Navigation

```
Users
├── Create user  →  admin.users.create
└── List users   →  admin.users.index
```
