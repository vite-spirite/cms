<?php

namespace App\Core\Module\Controllers;

use App\Core\Module\ModuleManager;
use Inertia\Inertia;
use Inertia\Response;

class ModuleListController
{
    public function __invoke(): Response
    {
        $npmAvailable = $this->asNodeAvailable();
        $moduleManager = app()->make(ModuleManager::class);

        return Inertia::render('Module::home', [
            'npmAvailable' => $npmAvailable,
            'modules' => $moduleManager->getAvailableModules(),
            'moduleEnabled' => $moduleManager->getActiveModules(),
        ]);
    }

    protected function asNodeAvailable(): bool
    {
        $result = null;
        exec('npm --version', $result);
        return !empty($result);
    }
}
