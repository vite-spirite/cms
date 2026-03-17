# Modules

Modules are the building blocks of the CMS. Each module is self-contained with its own routes, migrations, controllers,
and frontend assets. Core modules are always loaded, optional modules can be toggled from the admin interface.

## Getting Started

- [Creating a module](/modules/creating-module) — step-by-step guide to create your first module
- [Managing modules](/modules/managing-modules) — enable and disable modules from the admin UI
- [Module communication](/modules/module-communication) — events, `ModuleHelper`, and Inertia shared props
- [Extension points](/modules/extension-points) — inject Vue components into other modules' pages
- [Frontend conventions](/modules/frontend) — entry files, registries, composables, and TypeScript types
- [Creating a block](/modules/creating-block) — add custom blocks to the PageBuilder
- [Permissions reference](/modules/permissions-reference) — all permissions across all modules

## Core Modules

- [Auth](/modules/core/auth) — authentication, user management, events
- [Permissions](/modules/core/permissions) — roles, direct permissions, owner flag
- [Navigation](/modules/core/navigation) — sidebar navigation manager
