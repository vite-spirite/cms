# Creating a Module

Learn how to create a custom module for the CMS.

## Module structure

```
app/Modules/Blog/
├── module.json
├── Providers/
│   └── BlogServiceProvider.php
├── Controllers/
├── Requests/
├── Models/
├── Migrations/
├── Console/
│   ├── Commands/
│   └── schedule.php
├── Events/
├── Routes/
│   ├── web.php
│   └── api.php
├── Config/
└── Resources/
    ├── js/
    │   ├── extensions.ts
    │   ├── blocks.ts
    │   ├── fields.ts
    │   ├── Pages/
    │   └── Components/
    └── lang/
        └── en/
```

## Step 1: Create `module.json`

This file is required for the module to be discovered by the `ModuleManager`.

```json
{
    "name": "Blog",
    "display_name": "Blog",
    "description": "Manage blog posts",
    "version": "1.0.0",
    "type": "module",
    "provider": "App\\Modules\\Blog\\Providers\\BlogServiceProvider"
}
```

| Field          | Description                                                                      |
|----------------|----------------------------------------------------------------------------------|
| `name`         | Unique identifier, used by `ModuleHelper::when()` and in the UI. Case-sensitive. |
| `display_name` | Human-readable name shown in the module manager UI                               |
| `description`  | Short description shown in the module manager UI                                 |
| `version`      | Semantic version of the module                                                   |
| `type`         | `core` (always loaded) or `module` (can be toggled)                              |
| `provider`     | Fully qualified class name of the service provider                               |

## Step 2: Create the service provider

```php
<?php

namespace App\Modules\Blog\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class BlogServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Blog';

    protected array $permissions = [
        'post_create' => [
            'name'        => 'Create blog posts',
            'description' => 'Ability to create new blog posts',
        ],
        'post_edit' => [
            'name'        => 'Edit blog posts',
            'description' => 'Ability to edit existing blog posts',
        ],
        'post_delete' => [
            'name'        => 'Delete blog posts',
            'description' => 'Ability to delete blog posts',
        ],
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'Blog',
                'icon'  => 'i-lucide-book-open',
                'children' => [
                    ['label' => 'All posts',   'icon' => 'i-lucide-list', 'route' => 'blog.index'],
                    ['label' => 'Create post', 'icon' => 'i-lucide-plus', 'route' => 'blog.create'],
                ],
            ],
        ];
    }
}
```

The `$name` property must match the `name` field in `module.json`.

## Step 3: Create routes

### Web routes

```php
<?php
// app/Modules/Blog/Routes/web.php

Route::prefix('admin/blog')->middleware(['auth'])->group(function () {
    Route::get('/list', \App\Modules\Blog\Controllers\PostListController::class)->name('blog.index');
    Route::get('/create', \App\Modules\Blog\Controllers\PostCreateController::class)->name('blog.create')->middleware('can:post_create');
    Route::post('/create', \App\Modules\Blog\Controllers\PostCreateRequestController::class)->name('blog.create.request')->middleware('can:post_create');
});
```

### API routes

```php
<?php
// app/Modules/Blog/Routes/api.php
// Automatically prefixed with /api and named with api.

Route::get('/blog/posts', \App\Modules\Blog\Controllers\PostApiListController::class)->name('blog.posts');
```

## Step 4: Auto-loaded resources

The following resources are automatically loaded by `BaseModuleServiceProvider` — no manual registration needed.

| Resource     | Path                     | Notes                                                      |
|--------------|--------------------------|------------------------------------------------------------|
| Web routes   | `Routes/web.php`         | Loaded with `web` middleware                               |
| API routes   | `Routes/api.php`         | Loaded with `web` + `auth`, prefixed `/api`, named `api.*` |
| Migrations   | `Migrations/*.php`       | Run with `php artisan migrate`                             |
| Commands     | `Console/Commands/*.php` | Auto-discovered, registered in console mode only           |
| Schedule     | `Console/schedule.php`   | Required after all providers are booted                    |
| Config       | `Config/*.php`           | Merged using filename as key                               |
| Translations | `Resources/lang/*`       | Accessible via `trans('Blog::file.key')`                   |

### Schedule example

```php
<?php
// app/Modules/Blog/Console/schedule.php

\Illuminate\Support\Facades\Schedule::command('blog:cleanup')->daily();
```

### Config example

```php
<?php
// app/Modules/Blog/Config/blog.php
// Access via config('blog.posts_per_page')

return [
    'posts_per_page' => 10,
];
```

### Translation example

```php
<?php
// app/Modules/Blog/Resources/lang/en/messages.php

return [
    'created' => 'Post created successfully.',
];
```

```php
trans('Blog::messages.created')
```

## Step 5: Optional integrations

Use `ModuleHelper` to integrate with other modules without creating hard dependencies:

```php
public function boot(): void
{
    parent::boot();

    ModuleHelper::when('Logger', function () {
        // Log something when a post is created
    });

    ModuleHelper::when('PageBuilder', function () {
        $registry = $this->app->make(\App\Modules\PageBuilder\Services\BlockRegistry::class);
        $registry->register(\App\Modules\Blog\Blocks\PostBlock::class);
    });
}
```

## Step 6: Activate the module

Once your files are in place, go to the **Module manager** in the admin panel and toggle your module on. This will:

- Register the module as loaded in the database
- Trigger a frontend rebuild via `RebuildFrontendJob`

See [Managing Modules](/modules/managing-modules) for more details.

::: tip
After activating a new module for the first time, run `php artisan migrate` to apply its migrations.
:::
