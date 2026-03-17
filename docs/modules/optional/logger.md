# Logger Module

The Logger module provides a real-time logging bar displayed at the bottom of the admin dashboard. It records actions
performed across all modules and displays them in a live updating table.

## Overview

- Bottom bar with live log feed, auto-refreshed every 10 seconds
- Log levels: `debug`, `info`, `success`, `warning`, `error`
- Logs are scoped to the current session
- Context data displayed with syntax highlighting
- Automatic pruning via scheduled command
- `CmsLog` facade available to all modules

## Routes

### API

| Method | URI                 | Name               | Middleware                |
|--------|---------------------|--------------------|---------------------------|
| GET    | `/api/logger/since` | `api.logger.since` | `auth`, `can:logger_view` |

The `since` query parameter accepts a date string. If omitted, defaults to 5 minutes ago.

## Permissions

| Key           | Display Name | Description                                    |
|---------------|--------------|------------------------------------------------|
| `logger_view` | View logs    | Ability to view the logger bar and log entries |

## CmsLog facade

Any module can log entries using the `CmsLog` facade. Always wrap calls in `ModuleHelper::when('Logger')` to avoid
errors when the Logger module is disabled.

```php
use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;

ModuleHelper::when('Logger', function () use ($user) {
    CmsLog::info(
        category: 'MyModule',
        action:   'my.action',
        message:  'Something happened.',
        context:  ['user' => $user->toArray()],
        subject:  $user
    );
});
```

### Available methods

| Method                 | Level     | Notes                             |
|------------------------|-----------|-----------------------------------|
| `CmsLog::info(...)`    | `info`    | General informational events      |
| `CmsLog::success(...)` | `success` | Successful operations             |
| `CmsLog::warning(...)` | `warning` | Non-critical issues               |
| `CmsLog::error(...)`   | `error`   | Errors and failures               |
| `CmsLog::debug(...)`   | `debug`   | Only logged outside of production |

### Method signature

All methods share the same signature:

```php
CmsLog::info(
    string $category,  // Module or feature name, e.g. 'Auth', 'Gallery'
    string $action,    // Dot-notation action, e.g. 'user.created'
    string $message,   // Human-readable description
    array $context,    // Optional additional data stored as JSON
    ?Model $subject    // Optional polymorphic subject (any Eloquent model)
): void
```

## Log model

| Field          | Type        | Description                                    |
|----------------|-------------|------------------------------------------------|
| `level`        | `enum`      | `debug`, `info`, `success`, `warning`, `error` |
| `category`     | `string`    | Module or feature name                         |
| `action`       | `string`    | Dot-notation action identifier                 |
| `message`      | `string`    | Human-readable description                     |
| `context`      | `json`      | Optional additional data                       |
| `subject_type` | `string`    | Polymorphic subject model class                |
| `subject_id`   | `int`       | Polymorphic subject model ID                   |
| `user_id`      | `foreignId` | User who triggered the action                  |
| `ip_address`   | `string`    | Client IP address                              |
| `user_agent`   | `string`    | Client user agent                              |
| `url`          | `string`    | Request URL                                    |
| `created_at`   | `timestamp` | Indexed for performance                        |

## Artisan command

```bash
# Delete logs older than 30 days (default)
php artisan logger:prune

# Delete logs older than 7 days
php artisan logger:prune --days=7
```

A scheduled task runs `logger:prune --days=7` automatically every week.

## Extension points

The Logger module registers its `LoggerBar` component on the `layout.dashboard.bottom` extension point, injecting the
log bar into the dashboard layout without modifying it.

## Shared Inertia data

The Logger module shares the session start timestamp via Inertia to scope log fetching to the current session:

```php
Inertia::share([
    'start_session_at' => Inertia::optional(fn() => session()->get('start_session_at') ?? 
        tap(now()->toISOString(), fn($ts) => session(['start_session_at' => $ts])))
]);
```

The `LoggerBar` component uses this value as the `since` parameter when fetching logs, so only entries created after the
session started are displayed.

## Navigation

The Logger module does not register any navigation items. It is accessible only through the bottom bar in the dashboard.
