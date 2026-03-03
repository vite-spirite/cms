<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Modules\PageBuilder\Services\BlockRegistry;
use Inertia\Inertia;

class PageBuilderCreateController
{
    public function __invoke()
    {
        $blockRegistry = app(BlockRegistry::class);

        return Inertia::render('PageBuilder::builder', [
            'blocks' => $blockRegistry->definitions()
        ]);
    }
}
