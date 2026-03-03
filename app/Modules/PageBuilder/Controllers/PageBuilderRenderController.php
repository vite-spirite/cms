<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Modules\PageBuilder\Models\Page;
use Inertia\Inertia;

class PageBuilderRenderController
{
    public function __invoke(?string $slug = '/')
    {

        $page = Page::where('slug', $slug)->where('status', 'published')->first();

        if (!$page) {
            abort(404);
        }

        return Inertia::render('PageBuilder::render', [
            'content' => $page->content,
            'og_balises' => $page->og_balises,
            'title' => $page->title,
        ]);
    }
}
