# Creating a Module

Learn how to create a custom module for CMS.

## Module Structure

```
app/Modules/Blog/
├── module.json
├── Providers/
│   └── BlogServiceProvider.php
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
├── Migrations/
├── Routes/
│   ├── web.php
│   └── api.php
├── Resources/
│   ├── js/
│   └── views/
└── Config/
```

## Step 1: Create module.json

```json
{
    "name": "blog",
    "display_name": "Blog",
    "version": "1.0.0",
    "type": "module",
    "provider": "App\\Modules\\Blog\\Providers\\BlogServiceProvider"
}
```

## Step 2: Create the Service Provider

```php
<?php

namespace App\Modules\Blog\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class BlogServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Blog';

    protected array $permissions = [
        'blog.view_posts' => [
            'name' => 'View blog posts',
            'description' => 'Consult blog post in admin panel'
        ],
        'blog.create_posts' => [
            'name' => 'Create blog posts',
            'description' => 'Create new blog articles'
        ],
        'blog.edit_posts' => [
            'name' => 'Edit blog posts',
            'description' => 'Edit any blog posts'
        ],
        'blog.delete_posts' => [
            'name' => 'Delete blog posts',
            'description' => 'Delete any blog posts'
        ]
    ];
    
    public function getNavigations() : array
    {
        return [
            'label' => 'Blog',
            'icon' => 'i-heroicons-document-text',
            'route' => 'blog.index',
        ];
    }
}
```

## Step 3: Create Routes

```php
<?php
// app/Modules/Blog/Routes/web.php

use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Http\Controllers\PostController;

Route::resource('blog', PostController::class);
```

## Step 4: Activate the Module

The module will be auto-discovered. Navigate to the admin panel to activate it.

::: tip
Modules are automatically discovered when you run `composer dump-autoload`.
:::

## Next Steps

- [Core Modules](/modules/core-modules)
- [Adding Permissions](/examples/adding-permissions)
- [Extension System](/guide/extension-points)
