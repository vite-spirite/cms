<?php


namespace App\Core\Navigation\Service;

use App\Core\Navigation\Data\NavigationItem;

class NavigationManager
{
    /** @var \App\Core\Navigation\Data\NavigationItem[] * */
    protected array $items = [];


    public function registerMany(array $items): void
    {
        foreach ($items as $item) {
            $this->register($item);
        }
    }

    public function register(array|NavigationItem $item): void
    {
        if (is_array($item)) {
            $item = NavigationItem::fromArray($item);
        }

        $this->items[] = $item;
    }

    public function all(): array
    {
        return collect($this->items)->values()->map(function (NavigationItem $item) {
            return $item->toArray();
        })->toArray();
    }
}
