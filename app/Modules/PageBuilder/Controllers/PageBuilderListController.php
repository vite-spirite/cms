<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Modules\PageBuilder\Models\Page;
use Inertia\Inertia;

class PageBuilderListController
{
    public function __invoke()
    {
        $pages = Page::orderBy('updated_at', 'desc')->get();

        return Inertia::render('PageBuilder::home', [
            'pages' => $pages
        ]);
    }
}
