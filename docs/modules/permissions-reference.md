# Permissions Reference

Complete list of all permissions registered across all core and optional modules.

## Core modules

### Auth

| Key           | Display Name    | Description                      |
|---------------|-----------------|----------------------------------|
| `user_create` | Create new user | Ability to create a new user     |
| `user_edit`   | Edit user       | Ability to edit an existing user |
| `user_delete` | Delete user     | Ability to delete a user         |
| `user_view`   | View user       | Ability to view user list        |

### Permissions

| Key                 | Display Name       | Description                                   |
|---------------------|--------------------|-----------------------------------------------|
| `role_create`       | Create new roles   | Ability to create roles                       |
| `role_read`         | Read roles         | Ability to view roles                         |
| `role_update`       | Update roles       | Ability to edit roles                         |
| `role_delete`       | Delete roles       | Ability to delete roles                       |
| `role_assign`       | Assign roles       | Ability to assign roles to users              |
| `permission_assign` | Assign permissions | Ability to assign direct permissions to users |

### Module

| Key             | Display Name   | Description                       |
|-----------------|----------------|-----------------------------------|
| `module_manage` | Manage modules | Ability to load or unload modules |

## Optional modules

### Gallery

| Key              | Display Name   | Description                   |
|------------------|----------------|-------------------------------|
| `gallery_upload` | Upload gallery | Ability to upload media files |
| `gallery_delete` | Delete gallery | Ability to delete media files |

### Logger

| Key           | Display Name | Description                                    |
|---------------|--------------|------------------------------------------------|
| `logger_view` | View logs    | Ability to view the logger bar and log entries |

### PageBuilder

| Key           | Display Name | Description                    |
|---------------|--------------|--------------------------------|
| `page_create` | Create page  | Ability to create new pages    |
| `page_edit`   | Edit page    | Ability to edit existing pages |
| `page_delete` | Delete page  | Ability to delete pages        |

## Registering permissions in a module

Permissions are declared in the `$permissions` property of your service provider and automatically registered when the
module boots:

```php
protected array $permissions = [
    'my_permission' => [
        'name'        => 'Display name shown in UI',
        'description' => 'Description shown in UI',
    ],
];
```

::: tip
Permission keys should follow the `resource_action` naming convention (e.g. `post_create`, `post_delete`) to stay
consistent with the rest of the codebase.
:::
