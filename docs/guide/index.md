# Introduction

This CMS is a modular content management system built with Laravel, Vue 3, Inertia.js, and TypeScript. It is designed to
be extended through self-contained modules that can be enabled or disabled without touching core code.

## Key concepts

**Modules** are the building blocks of the application. Each module is a self-contained unit with its own routes,
migrations, controllers, Vue pages, and assets. Core modules are always loaded, optional modules can be toggled from the
admin interface.

**Extension points** allow modules to inject Vue components into pages of other modules without any direct dependency.
This means a module can add a section to the user creation form without modifying the Auth module.

**Events** are the standard communication channel between modules. A module dispatches an event, and any other module
can listen to it without the emitter knowing about the listener.

**Permissions** are declared by each module and automatically registered at boot time. The Permissions module provides
roles, direct user permissions, and an owner flag that bypasses all checks.

## Stack

| Layer    | Technology         |
|----------|--------------------|
| Backend  | Laravel 12         |
| Frontend | Vue 3 + TypeScript |
| Routing  | Inertia.js + Ziggy |
| UI       | Nuxt UI            |
| Styles   | Tailwind CSS       |
| State    | Pinia              |
| Build    | Vite               |

## Next steps

- [Installation](/guide/installation) — get the project running locally
- [Quick Start](/guide/quick-start) — create your first user and enable modules
- [Architecture](/guide/architecture) — understand the boot cycle and module system
- [Creating a module](/modules/creating-module) — build your first module
