<?php

namespace App\Core\Module;

use App\Core\Module\Models\Module;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleManager
{
    protected array $modules = [];

    protected array $activeModules = [];

    public function __construct(protected \Illuminate\Contracts\Foundation\Application $app)
    {
    }

    public function getActiveModules(): array
    {
        return $this->activeModules;
    }

    public function getAvailableModules(): array
    {
        return $this->modules;
    }

    public function getModule(string $moduleName): array|null
    {
        return $this->modules[$moduleName] ?? null;
    }

    public function isModuleLoaded(string $moduleName): bool
    {
        return in_array($moduleName, $this->activeModules);
    }

    public function discovers(): void
    {
        $this->modules = [];

        $this->discoversModules('Core');
        $this->discoversModules('Modules');
    }

    protected function discoversModules(string $appPath): void
    {
        $path = app_path($appPath);
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $jsonPath = $directory . '/module.json';

            if (File::exists($jsonPath)) {
                $data = json_decode(File::get($jsonPath), true);
                if ($data && isset($data['name'])) {

                    if (Arr::has($this->modules, $data['name'])) {
                        Log::error("Module '{$data['name']}' already exists.");
                        continue;
                    }

                    $this->modules[$data['name']] = $data;
                }
            }
        }
    }

    public function loadModules(string $type)
    {
        foreach ($this->modules as $module) {
            if (isset($module['type']) && $module['type'] == $type) {
                $this->registerProvider($module);
            }
        }
    }

    protected function registerProvider(array $module): void
    {
        $moduleName = $module['name'];
        $moduleProvider = $module['provider'];

        if (!$moduleName || !class_exists($moduleProvider)) {
            return;
        }

        if (in_array($moduleName, $this->activeModules)) {
            return;
        }

        $this->app->register($moduleProvider);
        $this->activeModules[] = $moduleName;
    }

    public function loadModule(string $moduleName): bool
    {
        $module = $this->modules[$moduleName] ?? null;

        if (!$module) {
            return false;
        }

        if (in_array($moduleName, $this->activeModules)) {
            return false;
        }

        Module::updateOrCreate(['name' => $moduleName], ['name' => $moduleName, 'loaded' => true, 'loaded_at' => now()->toISOString()]);
        $this->registerProvider($module);
        return true;
    }

    public function unloadModule(string $moduleName): bool
    {
        $module = $this->modules[$moduleName] ?? null;

        if (!$module) {
            return false;
        }

        if (!in_array($moduleName, $this->activeModules)) {
            return false;
        }

        Module::updateOrCreate(['name' => $moduleName], ['name' => $moduleName, 'loaded' => false]);
        $this->unloadProvider($moduleName);
        return true;

    }

    public function unloadProvider(string $moduleName): void
    {
        if (!in_array($moduleName, $this->activeModules)) {
            return;
        }

        $this->activeModules = array_filter(
            $this->activeModules,
            fn($name) => $name !== $moduleName
        );
    }

    public function loadStoredModules(): void
    {
        try {
            if (!\Schema::hasTable('modules')) {
                logger()->info("Stored 'modules' table does not exist.");
            }

            $moduleList = Module::orderBy('id', 'ASC')->where('loaded', true)->get();
            foreach ($moduleList as $dbModule) {
                $module = $this->modules[$dbModule->name] ?? null;
                if (!$module) {
                    Log::warning("Module '{$dbModule->name}' is stored but not found on disk.");
                    continue;
                }
                $this->registerProvider($module);
            }
        } catch (\Exception $exception) {
            logger()->info("Stored 'modules' table does not exist.", [$exception->getMessage()]);
        }

    }
}
