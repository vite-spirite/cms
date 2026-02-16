<?php


namespace App\Core\Navigation\Providers;

use App\Core\Module\BaseModuleServiceProvider;
use App\Core\Navigation\Service\NavigationManager;
use Inertia\Inertia;

class NavigationServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'navigation';
    protected array $permissions = [];

    public function register(): void
    {
        parent::register();

        $this->app->singleton(NavigationManager::class, function () {
            return new NavigationManager();
        });
    }

    public function boot(): void
    {
        parent::boot();

        $this->shareInertia();
    }

    protected function shareInertia(): void
    {
        Inertia::share([
            'navigation' => fn() => $this->app->make(NavigationManager::class)->all(),
        ]);
    }
}
