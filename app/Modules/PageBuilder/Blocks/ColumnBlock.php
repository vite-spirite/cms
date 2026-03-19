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
            'gap' => [
                'label' => 'Spacing:',
                'type' => 'int',
                'default' => 2,
            ],
            'width' => [
                'label' => 'Width:',
                'type' => 'select',
                'options' => [
                    'auto',
                    '1/2',
                    '1/3',
                    '2/3',
                    '1/4',
                    '3/4',
                    'full'
                ]
            ],
            'align_items' => [
                'label' => 'Alignment:',
                'type' => 'select',
                'options' => [
                    'start',
                    'center',
                    'end'
                ]
            ],
            'children' => [
                'label' => 'Children',
                'type' => 'blocks',
                'default' => []
            ]
        ];
    }

    public function data(): array
    {
        return [];
    }
}
