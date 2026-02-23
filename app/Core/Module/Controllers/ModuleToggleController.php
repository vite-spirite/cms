<?php

namespace App\Core\Module\Controllers;

use App\Core\Module\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ModuleToggleController
{
    public function __invoke(Request $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $moduleName = $request->input('module');

        if (!$moduleName) {
            return Redirect::back()->with(['error' => ['title' => 'Module not found']]);
        }

        $moduleManager = app()->make(ModuleManager::class);
        $module = $moduleManager->getModule($moduleName);

        if (!$module) {
            return Redirect::back()->with(['error' => ['title' => 'Module not found']]);
        }

        if ($moduleManager->loadModule($moduleName)) {
            dispatch(function () {
                exec('npm run build && npm run build:ssr');
            })->afterResponse();

            return Inertia::location(route('admin.home'));

        } else {
            $moduleManager->unloadModule($moduleName);

            \Artisan::call('cache:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            if (config('octane.server')) {
                \Artisan::call('octane:reload');
            }

            dispatch(function () {
                exec('npm run build && npm run build:ssr');
            })->afterResponse();

            return Inertia::location(route('admin.home'));

        }

        return Redirect::back()->with(['error' => ['title' => 'Manage module', 'description' => 'An error occured while deactivating this module']]);

    }
}
