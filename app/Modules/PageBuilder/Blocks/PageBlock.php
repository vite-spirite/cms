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
            'max_width' => [
                'label' => 'Width',
                'type' => 'select',
                'default' => 'md',
                'options' => [
                    'sm',
                    'md',
                    'xl',
                    'full'
                ],
                'required' => true,
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
            'bg_type' => [
                'label' => 'Background type',
                'type' => 'select',
                'default' => 'color',
                'options' => [
                    'color',
                    'image',
                ],
                'required' => true,
            ],
            'bg_color' => [
                'label' => 'Background color',
                'type' => 'color',
                'default' => '#aaa'
            ],
            'bg_image' => [
                'label' => 'Background image',
                'type' => 'text',
            ],
            'bg_overlay' => [
                'label' => 'Background overlay',
                'type' => 'int',
                'default' => 0,
            ],
            'text_color' => [
                'label' => 'Default text color',
                'type' => 'color',
                'default' => '#000',
                'required' => true,
            ],
            'force_height' => [
                'label' => 'Force height',
                'type' => 'bool',
                'default' => false,
            ],
            'children' => [
                'label' => 'Children',
                'type' => 'blocks',
                'default' => []
            ],
        ];
    }

    public static function type(): string
    {
        return 'page';
    }
}
