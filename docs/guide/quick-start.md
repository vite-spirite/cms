# Quick start

## After installation

### 1. Create first user

```bash
php artisan auth:create-user "John Doe" "john@example.com" --password="mySecurePassword"
```

:::warning
Replace the name, email, and password with your own information.
If the name contains spaces, wrap it in quotes.
:::

:::tip
The `--password` flag is optional. If not provided, you will be prompted to enter the password securely in the terminal.
:::

### 2. Assign owner status to a user

```bash
# Option 1: Specify email directly
php artisan permissions:owner make john@example.com

# Option 2: Interactive mode (will ask for email)
php artisan permissions:owner make
```

:::warning
The owner status grants **all permissions** and bypasses all permission checks.
This should only be assigned to a trusted user or used for initial configuration.
:::

:::tip
Use `php artisan permissions:owner list` to see all users with owner status.
:::

### 3. Access dashboard

Visit [http://localhost:8000/login](http://localhost:8000/login)

:::tip
Default development server can be started with:

```bash
php artisan serve
```

:::

## Troubleshooting

### User already exists error

If you get "User with email {email} already exists!", the email is already in the database.
Use a different email or delete the existing user first.

### Permission errors in browser

If you can't access certain pages after login:

1. Make sure you assigned owner status: `php artisan permissions:owner make`
2. Clear cache: `php artisan cache:clear`
3. Check that the Permission module is loaded in the admin panel

### Database errors

If you get database errors:

1. Make sure migrations are run: `php artisan migrate`
2. Check your `.env` database configuration
