# Permissions Module

The Permissions module provides Role-Based Access Control (RBAC) for the CMS. It manages roles, permissions, and their assignments to users.

## Architecture

The RBAC system is built on three main concepts:

1. **Permissions**: Fine-grained abilities (e.g., `role_create`, `permission_assign`)
2. **Roles**: Named collections of permissions that can be assigned to users
3. **Owners**: Special users who bypass all permission checks (superadmin)

Users gain permissions through:
- **Role assignment**: Inherit permissions from assigned roles
- **Direct assignment**: Get individual permissions via the `user_permissions` table
- **Owner status**: Implicit permission to all abilities

## PermissionRegistry

The `PermissionRegistry` class manages permission registration and synchronization with the database.

### Method Signatures

```php
public function register(string $module, string $permission, string $name, string $description): void
```
Registers a single permission.
- `$module`: Module name (e.g., `'permissions'`, `'pages'`)
- `$permission`: Unique permission identifier (e.g., `'role_create'`)
- `$name`: Display name for UI (e.g., `'Create new roles'`)
- `$description`: Human-readable description

```php
public function registerMany(string $module, array $permissions): void
```
Registers multiple permissions at once.
```php
$permissions = [
    'role_create' => [
        'name' => 'Create new roles',
        'description' => 'Create new roles ability',
    ],
    'role_read' => [
        'name' => 'Read roles',
        'description' => 'Read roles ability',
    ],
];
$registry->registerMany('permissions', $permissions);
```

```php
public function all(): array
```
Returns all registered permissions as an associative array.

```php
public function groupByModule(): array
```
Groups all permissions by their module name.

```php
public function sync(): void
```
Synchronizes registered permissions with the `permissions` table in the database. Creates new permissions, updates existing ones.

### Usage Example

In `PermissionServiceProvider`:
```php
protected array $permissions = [
    'role_create' => [
        'name' => 'Create new roles',
        'description' => 'Create new roles ability',
    ],
    'role_read' => [
        'name' => 'Read roles',
        'description' => 'Read roles ability',
    ],
    // ... more permissions
];

public function boot(): void
{
    $permissionRegistry = $this->app->make(PermissionRegistry::class);
    $permissionRegistry->registerMany('permissions', $this->permissions);
    $permissionRegistry->sync();
    $this->registerGates();
}
```

## Database Schema

### Migrations

1. **01_create_permission_table.php**
   - Table: `permissions`
   - Columns: `id`, `name` (unique), `module`
   - Index: `[module, name]`

2. **02_create_role_table.php**
   - Table: `roles`
   - Columns: `id`, `name`

3. **03_create_role_permission.php**
   - Table: `role_permission` (pivot table)
   - Columns: `id`, `role_id` (FK→roles), `permission_id` (FK→permissions)
   - Cascade delete on both sides

4. **04_create_user_role_table.php**
   - Table: `user_role` (pivot table)
   - Columns: `id`, `user_id` (FK→users), `role_id` (FK→roles)
   - Cascade delete on both sides

5. **05_add_owner_in_users_table.php**
   - Adds column: `is_owner` (boolean, default: false) to `users` table
   - Index: `is_owner`

6. **06_create_user_permissions_table.php**
   - Table: `user_permissions` (pivot table for direct permissions)
   - Columns: `id`, `user_id` (FK→users), `permission_id` (FK→permissions)
   - Cascade delete and update on both sides

## Models

### Permission

```php
namespace App\Core\Permissions\Models;

class Permission extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'module'];

    public function roles(): BelongsToMany
```

Represents a single permission (ability).

**Relationships:**
- `roles()`: Many-to-many with Role via `role_permission` table

### Role

```php
namespace App\Core\Permissions\Models;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function users(): BelongsToMany
    public function permissions(): BelongsToMany
```

Represents a named collection of permissions.

**Relationships:**
- `users()`: Many-to-many with User via `user_role` table
- `permissions()`: Many-to-many with Permission via `role_permission` table

### User Extensions

The `PermissionServiceProvider` extends the User model with macros and relationships:

```php
// Relationships
$user->roles()          // BelongsToMany Role via user_role
$user->permissions()    // BelongsToMany Permission via user_permissions

// Macros
$user->isOwner()                    // bool
$user->getPermissions()             // Collection of permission names
$user->hasPermission(string)        // bool
$user->hasAllPermissions(array)     // bool
$user->syncRoles(array $roleIds)    // void
$user->syncPermissions(array $permIds) // void
```

## HTTP Routes

### Web Routes (UI)

Prefix: `/admin/permissions`
Middleware: `auth`

| Method | Path | Controller | Permission | Name |
|--------|------|------------|------------|------|
| GET | `/roles/list` | `RoleListController` | `role_read` | `permissions.roles.list` |
| GET | `/roles/create` | `RoleCreateController` | `role_create` | `permissions.roles.create` |
| POST | `/roles/create` | `RoleCreateRequestController` | `role_create` | `permissions.roles.create.request` |
| GET | `/roles/edit/{role}` | `RoleUpdateController` | `role_update` | `permissions.roles.edit` |
| PUT | `/role/edit/{role}` | `RoleUpdateRequestController` | `role_update` | `permissions.roles.edit.request` |
| DELETE | `/roles/delete/{role}` | `RoleDeleteController` | (none) | `permissions.roles.delete` |

### API Routes

Prefix: `/api` (no explicit prefix in routes/api.php but standard Laravel structure)

| Method | Path | Controller | Permission | Name |
|--------|------|------------|------------|------|
| GET | `/roles/all` | `RoleApiAllController` | `role_read` | `roles.all` |
| GET | `/permissions/all` | `PermissionApiAllController` | `role_read` | `permissions.all` |
| GET | `/permissions/get/{user}` | `PermissionApiGetController` | `role_read` | `permissions.get` |
| GET | `/roles/get/{user}` | `RoleApiGetController` | `role_read` | `roles.get` |

## Built-in Permissions

The Permissions module registers these permissions automatically:

| Permission | Display Name | Description |
|------------|--------------|-------------|
| `role_create` | Create new roles | Create new roles ability |
| `role_read` | Read roles | Read roles ability |
| `role_update` | Update roles | Update roles ability |
| `role_delete` | Delete roles | Delete roles ability |
| `role_assign` | Assign roles | Assign roles to users |
| `permission_assign` | Assign permissions | Assign permissions to users |

## Events

### RoleCreated

Fired when a role is created via `RoleCreateRequestController`.

```php
namespace App\Core\Permissions\Events;

class RoleCreated
{
    public function __construct(public Role $role, public array $payload)
    // payload contains 'extensions' array from the create request
```

### RoleUpdated

Fired when a role is updated via `RoleUpdateRequestController`.

```php
namespace App\Core\Permissions\Events;

class RoleUpdated
{
    public function __construct(public Role $role, public array $payload)
    // payload contains 'extensions' array from the update request
```

Both events implement Laravel's event contract and can be listened to:

```php
\Event::listen(RoleCreated::class, function (RoleCreated $event) {
    // $event->role is the Role model
    // $event->payload contains extra data
});
```

## Owner Concept

An **Owner** is a special user with `is_owner = true` on the `users` table. Owners have implicit permission to all abilities.

### How Owner Checks Work

In `PermissionServiceProvider::registerGates()`:

```php
Gate::before(function ($user, $ability) {
    if (User::hasMacro('isOwner') && $user->isOwner()) {
        return true;  // Owner bypasses all checks
    }
});
```

The `Gate::before()` callback runs before any specific permission check. If the user is an owner, all ability checks automatically return `true`, regardless of their actual role or direct permissions.

### Owner Management

Use the `permissions:owner` CLI command:

```bash
# Make a user an owner
php artisan permissions:owner make user@example.com

# Revoke owner status
php artisan permissions:owner revoke user@example.com

# List all owners
php artisan permissions:owner list
```

## MakeOwnerCommand

Located at `Console/Commands/MakeOwnerCommand.php`

```php
protected $signature = 'permissions:owner {action : make|revoke|list} {email?}';
```

### Actions

**make** - Promote a user to owner
```bash
php artisan permissions:owner make
# Prompts for email if not provided

php artisan permissions:owner make john@example.com
# Fails if user not found or already an owner (unless confirmed)
```

**revoke** - Demote an owner back to normal user
```bash
php artisan permissions:owner revoke jane@example.com
# Fails if user not found or is not an owner
```

**list** - Display all owners
```bash
php artisan permissions:owner list
# Outputs table: id | name | email
```

## Permission Checking

### PHP (Backend)

Use Laravel's Gate facade to check permissions:

```php
use Illuminate\Support\Facades\Gate;

// Check single permission
if (Gate::allows('role_create')) {
    // User has role_create permission
}

if (Gate::denies('role_create')) {
    // User does not have role_create permission
}

// Direct user method calls
$user->hasPermission('role_create')           // bool
$user->hasAllPermissions(['role_read', 'role_update']) // bool

// Middleware (automatic on routes)
Route::post('/roles', ...)->middleware('can:role_create');
```

In controllers:
```php
$this->authorize('role_create');  // Throws AuthorizationException if denied

if (Gate::allows('role_create')) {
    // Proceed
}
```

### Vue (Frontend)

Use the `useGate` composable from `@/composables`:

```vue
<script setup lang="ts">
import { useGate } from '@/composables'
import type { Permission } from '@/types'

const { can, isOwner, capabilities } = useGate()

// Check single permission
if (can('role_create')) {
    // User has permission
}

// Check if user is owner
if (isOwner.value) {
    // User is an owner
}

// All user permissions
const perms: string[] = capabilities.value
</script>

<template>
    <button v-if="can('role_create')">Create Role</button>
    <div v-if="isOwner">Admin Panel</div>
</template>
```

The `useGate` composable reads from Inertia shared data populated by `PermissionServiceProvider::shareInertia()`:

```php
Inertia::share([
    'permissions' => [
        'capabilities' => fn() => Auth::check() ? Auth::user()->getPermissions() : [],
        'owner' => fn() => Auth::check() ? Auth::user()->isOwner() : false,
    ]
]);
```

## TypeScript Types

Located at `Resources/js/types/role.ts`:

```typescript
export type Permission = {
    id: number;
    name: string;
    module: string;
    description: string;
    display_name?: string;
};

export type Role = {
    id: number;
    name: string;
    permissions: Permission[];
    users: User[];
    permissions_count?: number;
    users_count?: number;
};
```

Use these in Vue components and API calls:

```typescript
import type { Role, Permission } from '@/types'

const role: Role = {
    id: 1,
    name: 'Editor',
    permissions: [...],
    users: [...],
    permissions_count: 5,
    users_count: 12,
}
```

## Form Requests

### CreateRoleRequest

```php
namespace App\Core\Permissions\Requests;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('role_create');
    }

    public function rules(): array
    {
        return [
            'name' => 'string|required|min:3',
            'permissions' => 'list',
            'permissions.*' => 'string',
            'extensions' => 'array'
        ];
    }
}
```

### UpdateRoleRequest

```php
namespace App\Core\Permissions\Requests;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('role_update');
    }

    public function rules(): array
    {
        return [
            'id' => 'bail|required|integer|exists:roles,id',
            'name' => 'string|required|min:3',
            'permissions' => 'list',
            'permissions.*' => 'string',
            'extensions' => 'array|sometimes'
        ];
    }
}
```

Both requests validate that the authenticated user has the required permission via the `authorize()` method. If unauthorized, a 403 Forbidden response is returned automatically.

## Integration with Other Modules

### User Events

The Permissions module listens to Auth module events:

- **UserCreated**: When a new user is created, syncs roles and permissions if the current user has `role_assign` or `permission_assign` capabilities
- **UserEdited**: When a user is edited, updates roles and permissions similarly

Logged via the Logger module if available.

### Permission Registry Syncing

When users are created or edited, the `PermissionRegistry::sync()` method is called to ensure the database has all registered permissions up to date.

## Service Provider

`PermissionServiceProvider` extends `BaseModuleServiceProvider` and handles:

1. **Boot-time Setup**
   - Extends User model with permissions methods and relationships
   - Registers gates for all permissions
   - Shares permission data with Inertia (frontend)
   - Listens to user lifecycle events

2. **Navigation**
   - Provides menu structure for the Permissions UI (Roles list/create)

3. **Singleton Registration**
   - Registers `PermissionRegistry` as a singleton for dependency injection
