<?php

namespace App\Modules\PageBuilder\Controllers;

use App\Modules\PageBuilder\Models\Page;
use App\Modules\PageBuilder\Services\BlockRegistry;
use Inertia\Inertia;

class PageBuilderEditController
{
    public function __invoke(Page $page)
    {

        $blockRegistry = app(BlockRegistry::class);
        $definitions = $blockRegistry->definitions();

        return Inertia::render('PageBuilder::builder', [
            'blocks' => $definitions,
            'page' => $page
        ]);
    }
}
