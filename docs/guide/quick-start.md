# Quick Start

After completing the [installation](/guide/installation), follow these steps to create your first user and give it full
access to the admin panel.

## Step 1: Create a user

```bash
php artisan auth:create-user "John Doe" john@example.com
```

You will be prompted for a password. The password must be at least 8 characters.

## Step 2: Make the user owner

The easiest way to get full access is to assign the owner flag to your user. The owner bypasses all permission checks
unconditionally.

```bash
php artisan permissions:owner make john@example.com
```

You can now log in at `/admin/login` with full access to everything.

## Step 3: Enable optional modules

Once logged in, navigate to the **Module manager** (`/admin`) and enable the modules you need.

::: warning
After enabling a module for the first time, run `php artisan migrate` to apply its migrations before using it.
:::

## That's it

You now have a fully working CMS with a superuser account. From here you can:

- [Create additional users](/modules/core/auth) and assign them roles and permissions
- [Create roles](/modules/core/permissions) and assign permissions to them
- [Build your first module](/modules/creating-module)
