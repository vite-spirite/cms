# API Reference

Technical reference for the core services, base classes, and events available to module developers.

## Base classes

- [BaseModuleProvider](/api/base-module-provider) — base class for all module service providers, handles auto-loading of
  routes, migrations, commands, config, translations, permissions, and navigation

## Services

- [ModuleHelper](/api/module-helper) — utility class for conditional module integrations
- [NavigationManager](/api/navigation-manager) — registers and resolves sidebar navigation items
- [PermissionRegistry](/api/permission-registry) — registers permissions and syncs them to the database

## Events

- [Events](/api/events) — all events dispatched across core and optional modules
