<?php

namespace App\Modules\Gallery\Providers;

use App\Core\Module\BaseModuleServiceProvider;
use App\Core\Module\ModuleHelper;
use App\Modules\Gallery\Blocks\MediaBlock;
use App\Modules\Logger\Facades\CmsLog;
use App\Modules\PageBuilder\Services\BlockRegistry;

class GalleryServiceProvider extends BaseModuleServiceProvider
{

    protected array $permissions = [
        'gallery_upload' => [
            'name' => 'upload gallery',
            'description' => 'upload gallery',
        ],
        'gallery_delete' => [
            'name' => 'delete gallery',
            'description' => 'delete gallery',
        ],
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => "Gallery",
                'icon' => 'i-lucide-images',
                'route' => 'gallery.list',
            ]
        ];
    }

    public function boot(): void
    {
        parent::boot();

        if (!\Schema::hasTable('medias')) {
            logger()->warning('The gallery module is loaded, but the migrations have not been executed.');

            ModuleHelper::when('Logger', function () {
                CmsLog::error('gallery', 'gallery.boot', 'The gallery module is loaded, but the migrations have not been executed.');
            });
        }

        ModuleHelper::when('PageBuilder', function () {
            $registry = $this->app->make(BlockRegistry::class);
            $registry->register(MediaBlock::class);
        });
    }
}
