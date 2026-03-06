<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Modules\PageBuilder\Models\Page;
use App\Modules\PageBuilder\Services\BlockRegistry;
use Inertia\Inertia;

class PageBuilderRenderController
{
    public function __invoke(?string $slug = '/')
    {

        $page = Page::where('slug', $slug)->where('status', 'published')->first();

        if (!$page) {
            abort(404);
        }

        $blockRegistry = app(BlockRegistry::class);
        return Inertia::render('PageBuilder::render', [
            'content' => $blockRegistry->render($page->content),
            'og_balises' => $page->og_balises,
            'title' => $page->title,
            'slug' => $page->slug,
            'id' => $page->id,
        ]);
    }
}
