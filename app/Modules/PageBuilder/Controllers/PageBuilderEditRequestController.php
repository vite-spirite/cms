<?php

namespace App\Modules\PageBuilder\Controllers;

use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;
use App\Modules\PageBuilder\Models\Page;
use App\Modules\PageBuilder\Requests\CreatePageRequest;
use App\Modules\PageBuilder\Services\BlockRegistry;
use Carbon\Carbon;

class PageBuilderEditRequestController
{
    public function __invoke(Page $page, CreatePageRequest $request)
    {
        $payload = $request->validated();

        $blockRegistry = app(BlockRegistry::class);
        $user = \Auth::user();
        $before = $page->toArray();

        $payload['content'] = $blockRegistry->serialize($payload['content']);
        
        $page->updated_by = $user->id;
        $page->fill($payload);

        if ($page->getOriginal('status') != 'published' && $page->status == 'published') {
            $page->published_at = Carbon::now();
        }

        $page->save();

        ModuleHelper::when('Logger', function () use ($page, $before, $user) {
            CmsLog::info('PageBuilder', 'page.updated', "{$user->name} updated page builder id: {$page->id}", ['before' => $before, 'after' => $page->toArray()], $page);
        });

        return \Redirect::back()->with(['success' => ['title' => 'Page updated successfully', 'description' => null]]);
    }
}
