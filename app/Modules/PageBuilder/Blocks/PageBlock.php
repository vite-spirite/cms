<?php


namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;


class PageBlock extends AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-rows-2';
    }

    public static function label(): string
    {
        return 'Page container';
    }

    public static function schema(): array
    {
        return [
            'color' => [
                'label' => 'Background color',
                'type' => 'color',
                'default' => '#ffffff',
            ],
            'text_color' => [
                'label' => 'Text color',
                'type' => 'color',
                'default' => '#000',
            ],
            'spacing_y' => [
                'label' => 'Spacing y',
                'type' => 'int',
                'default' => 4,
            ],
            'spacing_x' => [
                'label' => 'Spacing x',
                'type' => 'int',
                'default' => 4,
            ],
            'children' => [
                'label' => 'Children',
                'type' => 'blocks',
                'default' => []
            ]
        ];
    }

    public static function type(): string
    {
        return 'page';
    }
}
