<?php

namespace App\Core\Module;

use App\Core\Module\Models\Module;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ModuleManager extends ServiceProvider
{
    protected array $modules = [];

    protected array $activeModules = [];

    public function getActiveModules(): array
    {
        return $this->activeModules;
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

        if (Arr::has($this->activeModules, $moduleName)) {
            return;
        }

        $this->app->register($moduleProvider);
        $this->activeModules[] = $moduleName;
    }

    public function loadStoredModules(): void
    {
        $moduleList = Module::orderBy('id', 'ASC')->where('loaded', true)->get();
        foreach ($moduleList as $dbModule) {
            $module = $this->modules[$dbModule->name];
            $this->registerProvider($module);
        }
    }
}
