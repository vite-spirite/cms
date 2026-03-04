<?php


namespace App\Modules\PageBuilder\Controllers;

use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;
use App\Modules\PageBuilder\Models\Page;
use App\Modules\PageBuilder\Requests\CreatePageRequest;
use App\Modules\PageBuilder\Services\BlockRegistry;
use Illuminate\Support\Carbon;

class PageBuilderCreateRequestController
{
    public function __invoke(CreatePageRequest $request)
    {
        $user = \Auth::user();

        if (!$user) {
            return \Redirect::back();
        }

        $blockRegistry = app(BlockRegistry::class);
        $payload = $request->validated();
        $content = $this->regenerateIds($payload['content']);
        $content = $blockRegistry->serialize($content);

        if (!$payload['slug']) {
            $date = Carbon::now()->timestamp;
            $payload['slug'] = \Str::slug($date . ' ' . $payload['title']);
        }

        $page = Page::create([
            ...$payload,
            'content' => $content,
            'updated_by' => $user->id,
        ]);

        ModuleHelper::when('Logger', function () use ($page, $user) {
            CmsLog::info('PageBuilder', 'page.created', "{$user->name} created page builder id: {$page->id}", $page->toArray(), $page);
        });

        return \Redirect::route('page.list')->with(['success' => ['title' => 'Page created', 'description' => 'Page created successfully!']]);
    }

    private function regenerateIds(array $blocks): array
    {
        return array_map(function ($block) {
            $block['id'] = (string)\Str::uuid();

            if (!empty($block['data']['children'])) {
                $block['data']['children'] = $this->regenerateIds($block['data']['children']);
            }

            return $block;

        }, $blocks);
    }
}
