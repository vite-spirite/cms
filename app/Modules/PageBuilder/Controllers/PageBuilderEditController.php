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

        $pageArr = $page->toArray();
        $pageArr['content'] = $blockRegistry->render($pageArr['content']);

        return Inertia::render('PageBuilder::builder', [
            'blocks' => $definitions,
            'page' => $pageArr
        ]);
    }
}
