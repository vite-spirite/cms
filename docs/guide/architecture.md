# Architecture

This page explains the overall structure of the CMS, how modules are discovered and loaded, and the boot cycle that ties
everything together.

## Directory structure

```
app/
├── Core/                   # Core modules, always loaded
│   ├── Auth/
│   ├── Module/
│   ├── Navigation/
│   └── Permissions/
├── Modules/                # Optional modules, can be toggled
│   ├── Gallery/
│   ├── Logger/
│   └── PageBuilder/
├── Providers/
│   └── AppServiceProvider.php
└── Jobs/
    └── RebuildFrontendJob.php
```

Each module is self-contained and follows the same structure:

```
ModuleName/
├── module.json             # Module metadata and provider reference
├── Providers/              # Service provider
├── Controllers/
├── Requests/
├── Models/
├── Migrations/
├── Events/
├── Console/
│   ├── Commands/
│   └── schedule.php
├── Routes/
│   ├── web.php
│   └── api.php
├── Config/
└── Resources/
    ├── js/
    └── lang/
```

## Core vs optional modules

|                           | Core        | Optional       |
|---------------------------|-------------|----------------|
| Location                  | `app/Core/` | `app/Modules/` |
| `type` in `module.json`   | `core`      | `module`       |
| Always loaded             | yes         | no             |
| Can be toggled            | no          | yes            |
| Stored in `modules` table | no          | yes            |

Core modules provide the foundational services that the CMS depends on (authentication, permissions, navigation, module
management). Optional modules add features on top and can be enabled or disabled without affecting the rest of the
application.

## Boot cycle

The following sequence happens on every request:

```
AppServiceProvider::boot()
│
├── ModuleManager::discovers()
│   ├── Scans app/Core/ for module.json files
│   └── Scans app/Modules/ for module.json files
│
├── ModuleManager::loadModules('core')
│   └── Registers each core module's service provider
│       └── BaseModuleServiceProvider::boot()
│           ├── registerRoutes()
│           ├── registerMigrations()
│           ├── registerCommands()
│           ├── registerSchedule()
│           ├── registerConfig()
│           ├── registerTranslations()
│           ├── registerNavigations()
│           └── registerPermissions()
│
└── ModuleManager::loadStoredModules()
    ├── Queries modules table for loaded = true
    └── Registers each optional module's service provider
        └── (same boot sequence as above)
```

## ModuleManager

The `ModuleManager` is the central piece of the module system. It is bound as a singleton in `AppServiceProvider` and is
responsible for:

- **Discovery** — scanning the filesystem for `module.json` files
- **Loading** — registering service providers with the application
- **State** — tracking which modules are currently active
- **Persistence** — reading and writing the loaded state to the `modules` database table

```php
// Available anywhere via the service container
$moduleManager = app(\App\Core\Module\ModuleManager::class);

$moduleManager->getActiveModules();   // ['Auth', 'Permissions', 'Navigation', ...]
$moduleManager->getAvailableModules(); // all discovered modules
$moduleManager->isModuleLoaded('Gallery'); // bool
```

## Frontend architecture

The frontend follows the same modular pattern. Each module ships its own Vue pages, components, and registration files
that are bundled together at build time.

```
app/Modules/MyModule/Resources/js/
├── extensions.ts   # Registers components onto extension points
├── blocks.ts       # Registers PageBuilder blocks
├── fields.ts       # Registers PageBuilder field types
├── Pages/          # Inertia pages
└── Components/     # Vue components
```

Three registries handle frontend extensibility:

- **ExtensionRegistry** — maps extension point names to Vue components
- **BlockRegistry** — maps block type strings to async Vue components
- **FieldRegistry** — maps field type strings to async Vue components

When a module is toggled, `RebuildFrontendJob` runs `npm run build` to regenerate the frontend bundle with the updated
set of active modules.

## Data flow

```
HTTP Request
│
├── Laravel Router → Controller
│   └── Inertia::render('Module::Page', [...props])
│
├── HandleInertiaRequests middleware
│   └── Merges shared props (auth, navigation, permissions, flash messages)
│
└── Inertia Response
    └── Vue page receives props via usePage()
```

Shared props are contributed by multiple modules via `Inertia::share()` in their service providers, and merged together
before each response.
