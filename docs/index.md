---
layout: home

hero:
    name: CMS
    text: Modular CMS for Modern Web
    tagline: Built with Laravel, Vue 3, Inertia.js, and TypeScript
    actions:
        -   theme: brand
            text: Get Started
            link: /guide/installation
        -   theme: alt
            text: View on GitHub
            link: https://github.com/vitespirite/cms

features:
    -   icon: 🧩
        title: Modular Architecture
        details: Create, activate, and deactivate modules without modifying core code. Each module is self-contained with its own routes, migrations, and views.

    -   icon: 🔐
        title: Advanced Permissions
        details: Hybrid permission system with role-based access control plus direct user permissions.

    -   icon: 🎨
        title: Extension System
        details: Extend existing pages and forms using extension points. Modules can add content to any page without hard dependencies.

    -   icon: ⚡
        title: Modern Stack
        details: Laravel 12, Vue 3 Composition API, Inertia.js, TypeScript, NuxtUI, and Tailwind CSS for a blazing-fast developer experience.

    -   icon: 🚀
        title: Developer-Friendly
        details: Clear conventions, auto-discovery of modules, and minimal configuration. Get started in minutes, not hours.

    -   icon: 📦
        title: UI-Driven Workflow
        details: Install modules, run migrations, manage permissions, and configure everything through an intuitive web interface.
---

## Quick Example

Create a new module in minutes:

```php
// app/Modules/Gallery/Providers/GalleryServiceProvider.php
class GalleryServiceProvider extends BaseModuleServiceProvider
{

    protected array $permissions = [
        'gallery_upload' => [
            'name' => 'upload gallery',
            'description' => 'upload gallery',
        ],
        'gallery_delete' => [
            'name' => 'delete gallery',
            'description' => 'delete gallery',
        ],
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => "Gallery",
                'icon' => 'i-lucide-images',
                'route' => 'gallery.list',
            ]
        ];
    }
}
```

That's it! Your module is auto-discovered and ready to use.

## What makes it stand out?

- **True Modularity**: Modules are completely self-contained
- **Zero Configuration**: Auto-discovery of routes, migrations, and commands
- **Extension Points**: Add functionality to existing pages without forking
- **Modern DX**: Full TypeScript support, hot module reloading, and more

[Get Started →](/guide/installation)
