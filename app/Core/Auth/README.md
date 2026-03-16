# Auth Module

The Auth module provides user authentication and user management functionality for the CMS. It handles user login/logout, user CRUD operations, and integrates with the permissions system for fine-grained access control.

## Purpose

- **User Authentication**: Handle admin login/logout with session management
- **User Management**: Create, read, update, and delete user accounts
- **Permission Integration**: Control access to user management features through permission gates
- **Event System**: Dispatch events when users are created or modified for logging and extension hooks

## User Model

**Location**: `App\Core\Auth\Models\User`

The `User` model extends Laravel's `Authenticatable` class and uses the following traits:

- **HasFactory**: Factory support for testing
- **Notifiable**: Laravel notification support
- **Macroable**: Runtime method registration support

### Fillable Attributes

```php
protected $fillable = [
    'name',
    'email',
    'password',
];
```

### Hidden Attributes

The following attributes are hidden during serialization:

```php
protected $hidden = [
    'password',
    'remember_token',
];
```

### Casts

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

## Routes

All auth routes are prefixed with `/admin` and grouped together.

### Authentication Routes

| Method | Route | Handler | Middleware | Description |
|--------|-------|---------|-----------|-------------|
| GET | `/admin/login` | `LoginController` | `guest` | Show login form |
| POST | `/admin/login` | `LoginRequestController` | `guest` | Process login request |
| POST | `/admin/logout` | `LogoutController` | `auth` | Logout and destroy session |

### User Management Routes

All user management routes require `auth` middleware.

| Method | Route | Handler | Middleware | Description |
|--------|-------|---------|-----------|-------------|
| GET | `/admin/users/create` | `CreateUserController` | `can:user_create` | Show create user form |
| POST | `/admin/users/create` | `CreateUserRequestController` | `can:user_create` | Process user creation |
| GET | `/admin/users/list` | `UserListController` | `auth` | List all users |
| GET | `/admin/users/edit/{user}` | `EditUserController` | `can:user_edit` | Show edit user form |
| PUT | `/admin/users/edit` | `EditUserRequestController` | `can:user_edit` | Process user update |
| DELETE | `/admin/users/delete/{user}` | `DeleteUserController` | `can:user_delete` | Delete user |

**Route Names**:
- `login` - Login page
- `admin.login.request` - Login submission
- `admin.logout` - Logout
- `admin.users.create` - Create user page
- `admin.users.create.request` - Create user submission
- `admin.users.index` - User list page
- `admin.users.edit` - Edit user page
- `admin.users.edit.request` - Edit user submission
- `admin.users.delete` - Delete user

## Form Validation

### Login Validation

**Class**: `App\Core\Auth\Requests\LoginRequest`

```php
'email' => 'required|email',
'password' => 'required|min:8|string',
'remember' => 'boolean',
```

### Create User Validation

**Class**: `App\Core\Auth\Requests\CreateUserRequest`

Authorization requires `user_create` permission.

```php
'email' => 'email|required|unique:users,email',
'password' => 'required|min:6|confirmed',
'name' => 'required|string|min:3',
'extensions' => 'sometimes|array'
```

### Edit User Validation

**Class**: `App\Core\Auth\Requests\EditUserRequest`

Authorization requires `user_edit` permission.

```php
'id' => 'required|exists:users,id',
'email' => 'required|email',
'name' => 'required',
'password' => 'nullable|confirmed|min:8',
'extensions' => 'sometimes|array',
```

## Events

The Auth module dispatches events for user operations, allowing other modules to hook into these actions.

### UserCreated Event

**Class**: `App\Core\Auth\Events\UserCreated`

**Fired When**: A new user is successfully created

**Constructor**:
```php
public function __construct(public User $user, public array $payload)
```

**Payload**: Contains the `extensions` array from the creation request

**Example Listener**:
```php
\Event::listen(UserCreated::class, function (UserCreated $event) {
    // $event->user contains the created User model
    // $event->payload contains extensions data
});
```

### UserEdited Event

**Class**: `App\Core\Auth\Events\UserEdited`

**Fired When**: An existing user is successfully updated

**Constructor**:
```php
public function __construct(public User $user, public array $payload)
```

**Payload**: Contains the `extensions` array from the edit request

**Example Listener**:
```php
\Event::listen(UserEdited::class, function (UserEdited $event) {
    // $event->user contains the updated User model
    // $event->payload contains extensions data
});
```

## Permissions

The Auth module registers four permissions for controlling access to user management features:

| Permission | Name | Description |
|-----------|------|-------------|
| `user_create` | Create new user | Can create new user accounts |
| `user_edit` | Edit user | Can edit existing user accounts |
| `user_delete` | Delete user | Can delete user accounts |
| `user_view` | View user | Can view user information |

**Integration with Permissions Module**: When the Permissions module is active, the Auth module listens to `RoleCreated` and `RoleUpdated` events to synchronize user-role assignments if the current user has the `role_assign` permission.

## Controllers

### LoginController

Renders the login form using Inertia Vue component `Auth::Login`.

### LoginRequestController

Processes login form submission:
- Validates credentials using `LoginRequest`
- Uses `Auth::attempt()` with email and password
- Supports "remember me" functionality
- Redirects to `admin.home` on success
- Returns to login form with error on failure

### LogoutController

Handles user logout:
- Calls `Auth::logout()`
- Invalidates session
- Regenerates CSRF token
- Redirects to login page

### CreateUserController

Renders the user creation form using Inertia Vue component `Auth::CreateUser`.

### CreateUserRequestController

Processes user creation:
- Validates request using `CreateUserRequest`
- Creates new User model with name, email, and password
- Dispatches `UserCreated` event with extensions payload
- Logs action via Logger module if available
- Redirects to user list on success

### EditUserController

Renders the user edit form with current user data using Inertia Vue component `Auth::EditUser`.

### EditUserRequestController

Processes user update:
- Validates request using `EditUserRequest`
- Retrieves user by ID
- Updates email if changed (validates uniqueness)
- Updates password if provided
- Updates name if provided
- Dispatches `UserEdited` event with extensions payload
- Logs action with before/after data via Logger module if available
- Redirects to user list on success

### UserListController

Fetches all users and renders list using Inertia Vue component `Auth::ListUser`.

### DeleteUserController

Deletes a user by ID and redirects back with success message.

## CLI Command

### Create User Command

**Command**: `auth:create-user`

**Signature**:
```bash
php artisan auth:create-user {name} {email} {--password=}
```

**Arguments**:
- `name`: The user's full name (required)
- `email`: The user's email address (required)

**Options**:
- `--password=`: The password (optional, will be prompted if not provided)

**Example Usage**:

```bash
# Create user with prompted password
php artisan auth:create-user "John Doe" "john@example.com"

# Create user with provided password
php artisan auth:create-user "Jane Smith" "jane@example.com" --password="secret123"
```

**Behavior**:
- Validates that email is not already in use
- Returns error code 1 if user with email already exists
- Returns success code 0 on successful creation
- Outputs created user's ID, name, and email in table format

## Frontend Vue Components

The Auth module uses the following Inertia Vue pages:

- **Auth::Login** - Login form page
- **Auth::CreateUser** - User creation form page
- **Auth::EditUser** - User edit form page
- **Auth::ListUser** - User list page with all users

## Service Provider

**Class**: `App\Core\Auth\Providers\AuthServiceProvider`

Extends `BaseModuleServiceProvider` and handles:

- Permission registration
- Navigation menu configuration
- Inertia data sharing of all users
- Event listeners for role creation/update when Permissions module is active

### Navigation Menu

The Auth module registers navigation items for user management:

```
users
├── create user (route: admin.users.create)
└── list users (route: admin.users.index)
```

### Inertia Shared Data

All users are shared via Inertia as `users` prop, available in all frontend components.

## Integration Points

### Logger Module Integration

When the Logger module is active:
- User creation is logged with action `user.created`
- User updates are logged with action `users.updated`
- Logs include user data and change details

### Permissions Module Integration

- Role creation/update synchronizes users to roles
- User management routes are gated by permissions
- Form requests authorize actions based on permissions

## Database

The users table includes:
- `id` (primary key)
- `name` (string)
- `email` (string, unique)
- `password` (hashed string)
- `email_verified_at` (nullable timestamp)
- `remember_token` (nullable string)
- Timestamps (created_at, updated_at)

See migration: `App\Core\Auth\Migrations\00_create_users_table`
