<?php

namespace App\Modules\PageBuilder\Blocks;

class ColumnBlock extends \App\Modules\PageBuilder\Contracts\AbstractBlock
{
    public static function type(): string
    {
        return 'column';
    }

    public static function icon(): string
    {
        return 'i-lucide-rows-2';
    }

    public static function label(): string
    {
        return 'Column';
    }

    public static function schema(): array
    {
        return [
            'space_y' => [
                'label' => 'Space Y:',
                'type' => 'int',
                'default' => 2,
            ],
            'children' => [
                'label' => 'Children',
                'type' => 'blocks',
                'default' => []
            ]
        ];
    }

    public function toArray(): array
    {
        $base = parent::toArray();

        $base['children'] = $this->data['children'] ?? [];

        return $base;
    }
}
