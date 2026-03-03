<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;
use App\Modules\PageBuilder\Models\Page;

class PageBuilderDeleteController
{
    public function __invoke(Page $page)
    {
        $user = \Auth::user();

        $page->status = 'archived';
        $page->updated_by = $user->id;

        $page->save();
        $page->delete();

        ModuleHelper::when('Logger', function () use ($page, $user) {
            CmsLog::info('PageBuilder', 'page.deleted', "{$user->name} deleted page {$page->title}", ['page_id' => $page->id], $page);
        });

        return \Redirect::back()->with(['success' => ['title' => 'Page has been deleted.']]);
    }
}
