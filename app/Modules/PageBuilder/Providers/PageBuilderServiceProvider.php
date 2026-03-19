<?php


namespace App\Modules\PageBuilder\Providers;

use App\Core\Module\BaseModuleServiceProvider;
use App\Modules\PageBuilder\Blocks\ButtonBlock;
use App\Modules\PageBuilder\Blocks\ColumnBlock;
use App\Modules\PageBuilder\Blocks\ImageBlock;
use App\Modules\PageBuilder\Blocks\PageBlock;
use App\Modules\PageBuilder\Blocks\RowBlock;
use App\Modules\PageBuilder\Blocks\SeparatorBlock;
use App\Modules\PageBuilder\Blocks\SpacerBlock;
use App\Modules\PageBuilder\Blocks\TextBlock;
use App\Modules\PageBuilder\Services\BlockRegistry;

class PageBuilderServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'pagebuilder';
    protected array $permissions = [
        'page_edit' => [
            'name' => 'Edit page',
            'description' => 'Edit page content',
        ],
        'page_create' => [
            'name' => 'Create page',
            'description' => 'Create page content',
        ],
        'page_delete' => [
            'name' => 'Delete page',
            'description' => 'Delete page content',
        ]
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'Page builder',
                'icon' => 'i-lucide-brick-wall',
                'children' => [
                    [
                        'label' => 'List',
                        'route' => 'page.list',
                        'icon' => 'i-lucide-brick-wall',
                    ],
                    [
                        'label' => 'Create',
                        'route' => 'page.create',
                        'icon' => 'i-lucide-brick-wall',
                    ]
                ]
            ]
        ];
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(BlockRegistry::class, function () {
            return new BlockRegistry();
        });

        $this->app->alias(BlockRegistry::class, 'block-registry');

        $this->registerBlocks();
    }

    public function registerBlocks(): void
    {
        $this->app->booted(function () {
            $blockRegistry = $this->app->make(BlockRegistry::class);
            $blockRegistry->registerMany([
                TextBlock::class,
                ImageBlock::class,
                ColumnBlock::class,
                RowBlock::class,
                PageBlock::class,
                SpacerBlock::class,
                SeparatorBlock::class,
                ButtonBlock::class
            ]);
        });
    }
}
