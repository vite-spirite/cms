# Permissions Module

The Permissions module provides a hybrid access control system combining **roles**, **direct user permissions**, and an
**owner** flag. It is a **core module**, always loaded and cannot be disabled.

## Overview

- Role-based access control (RBAC)
- Direct permission assignment per user
- Owner flag that bypasses all permission checks
- Gates automatically defined for every registered permission
- Integrates with the Auth module via events to sync roles/permissions on user create/edit

## How it works

The permission system has three layers, evaluated in order:

1. **Owner** — if `user.is_owner` is `true`, all gates return `true` unconditionally
2. **Role permissions** — permissions inherited from the roles assigned to the user
3. **Direct permissions** — permissions assigned directly to the user, independent of roles

A user has access to a permission if **any** of these layers grants it.

```
User
├── is_owner = true  →  bypasses everything
├── roles[]
│   └── role.permissions[]  →  inherited permissions
└── permissions[]  →  direct permissions
```

## Routes

### Web

| Method | URI                                      | Name                               | Middleware                |
|--------|------------------------------------------|------------------------------------|---------------------------|
| GET    | `/admin/permissions/roles/list`          | `permissions.roles.list`           | `auth`, `can:role_read`   |
| GET    | `/admin/permissions/roles/create`        | `permissions.roles.create`         | `auth`, `can:role_create` |
| POST   | `/admin/permissions/roles/create`        | `permissions.roles.create.request` | `auth`, `can:role_create` |
| GET    | `/admin/permissions/roles/edit/{role}`   | `permissions.roles.edit`           | `auth`, `can:role_update` |
| PUT    | `/admin/permissions/role/edit/{role}`    | `permissions.roles.edit.request`   | `auth`, `can:role_update` |
| DELETE | `/admin/permissions/roles/delete/{role}` | `permissions.roles.delete`         | `auth`, `can:role_delete` |

### API

| Method | URI                           | Name                  | Middleware              |
|--------|-------------------------------|-----------------------|-------------------------|
| GET    | `/api/roles/all`              | `api.roles.all`       | `auth`, `can:role_read` |
| GET    | `/api/roles/get/{user}`       | `api.roles.get`       | `auth`, `can:role_read` |
| GET    | `/api/permissions/all`        | `api.permissions.all` | `auth`, `can:role_read` |
| GET    | `/api/permissions/get/{user}` | `api.permissions.get` | `auth`, `can:role_read` |

## Permissions

| Key                 | Display Name       | Description                                   |
|---------------------|--------------------|-----------------------------------------------|
| `role_create`       | Create new roles   | Ability to create roles                       |
| `role_read`         | Read roles         | Ability to view roles                         |
| `role_update`       | Update roles       | Ability to edit roles                         |
| `role_delete`       | Delete roles       | Ability to delete roles                       |
| `role_assign`       | Assign roles       | Ability to assign roles to users              |
| `permission_assign` | Assign permissions | Ability to assign direct permissions to users |

---

## Owner flag

The owner is a superuser that bypasses all permission checks. There can only be one owner at a time.

Managing the owner is done exclusively via Artisan:

```bash
# Assign owner to a user
php artisan permissions:owner make john@example.com

# Revoke owner from a user
php artisan permissions:owner revoke john@example.com

# List current owners
php artisan permissions:owner list
```

::: warning
Assigning a new owner when one already exists will prompt a confirmation, as it replaces the existing one.
:::

## Checking permissions

### PHP

```php
// In a controller
Gate::allows('role_create');

// Via middleware on a route
Route::get('/roles/create', ...)->middleware('can:role_create');

// On the user model directly
$user->hasPermission('role_create');
$user->hasAllPermissions(['role_create', 'role_update']);
$user->getPermissions(); // returns a collection of permission names
```

### Vue

```ts
import {useGate} from '@modules/Module/Composables/useGate';

const gate = useGate();

gate.can('role_create');           // true / false
gate.canAny(['role_create', 'role_update']); // true if at least one matches
```

The `permissions` prop is shared globally via Inertia:

```ts
// Available on every page as page.props.permissions
type Props = {
    capabilities: string[],  // list of permission names the user has
    owner: boolean,           // true if the user is owner
}
```

## User model extensions

The Permissions module extends the `User` model at runtime using Laravel macros and `resolveRelationUsing`. No changes
are made to the User class itself.

**Relations added:**

```php
$user->roles();       // BelongsToMany → Role
$user->permissions(); // BelongsToMany → Permission (direct)
```

**Methods added:**

```php
$user->isOwner(): bool
$user->getPermissions(): Collection        // merged role + direct permissions
$user->hasPermission(string $permission): bool
$user->hasAllPermissions(array $permissions): bool
$user->syncRoles(array $roleIds): void
$user->syncPermissions(array $permissionIds): void
```

## Events

### `RoleCreated`

Dispatched after a role is successfully created.

```php
use App\Core\Permissions\Events\RoleCreated;

public Role $role;
public array $payload; // validated form data including 'extensions'
```

### `RoleUpdated`

Dispatched after a role is successfully updated.

```php
use App\Core\Permissions\Events\RoleUpdated;

public Role $role;
public array $payload;
```

::: tip
The Auth module listens to these events to sync users assigned to a role directly from the role edit form.
:::

## Integration with Auth module

When a user is created or edited, the Permissions module listens to `UserCreated` and `UserEdited` to sync roles and
direct permissions from the `extensions` payload:

```php
// extensions payload expected structure
[
    'roles' => [1, 2],           // role IDs to sync
    'permissions' => ['role_read', 'user_view'], // permission names to sync
]
```

This is handled automatically by the `AssignRole` and `AssignPermission` Vue components registered on the
`users.create.end` and `users.edit.end` extension points.

## Extension points

| Point name               | Location                       | Props passed                  |
|--------------------------|--------------------------------|-------------------------------|
| `role.create.form.start` | Top of the create role form    | `v-model` (extensions)        |
| `role.create.form.end`   | Bottom of the create role form | `v-model` (extensions)        |
| `role.update.form.start` | Top of the edit role form      | `v-model`, `members` (User[]) |
| `role.update.form.end`   | Bottom of the edit role form   | `v-model`, `members` (User[]) |

The Auth module uses `role.update.form.end` and `role.create.form.end` to inject the user assignment table (`AssignRole`
component).

## Navigation

```
Permissions
├── List roles   →  permissions.roles.list
└── Create roles →  permissions.roles.create
```
