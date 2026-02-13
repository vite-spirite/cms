<?php

namespace App\Core\Module;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ModuleManager extends ServiceProvider
{
    protected array $modules = [];

    protected array $activeModules = [];

    public function discovers(): void
    {
        $this->modules = [];

        $coreModules = $this->discoversModules('Core');
        $modules = $this->discoversModules('Modules');

        $this->modules = array_merge($coreModules, $modules);
    }

    protected function discoversModules(string $appPath): array
    {
        $modules = [];
        $path = app_path($appPath);
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $jsonPath = $directory.'/module.json';
            if (File::exists($jsonPath)) {
                $data = json_decode(File::get($jsonPath), true);
                if ($data && isset($data['name'])) {
                    $modules[$data['name']] = $data;
                }
            }
        }

        return $modules;
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

        if (! $moduleName || ! class_exists($moduleProvider)) {
            return;
        }

        if (Arr::has($this->activeModules, $moduleName)) {
            return;
        }

        $this->app->register($moduleProvider);
        $this->activeModules[] = $moduleName;
    }
}
