<?php

namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class SeparatorBlock extends AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-minus';
    }

    public static function label(): string
    {
        return 'Separator';
    }

    public static function schema(): array
    {
        return [
            'border_style' => [
                'label' => 'Line style',
                'type' => 'select',
                'options' => [
                    'dashed',
                    'dotted',
                    'inset',
                    'solid',
                    'double',
                    'groove',
                    'ridge',
                    'outset'
                ],
                'default' => 'solid',
            ],
            'color' => [
                'label' => 'Color',
                'type' => 'color',
                'default' => '#212121'
            ],
            'width' => [
                'label' => 'Width',
                'type' => 'int',
                'default' => 4,
            ]
        ];
    }

    public static function type(): string
    {
        return 'separator';
    }
}
