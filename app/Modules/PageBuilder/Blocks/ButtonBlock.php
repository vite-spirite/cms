<?php

namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class ButtonBlock extends AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-link';
    }

    public static function label(): string
    {
        return 'Button';
    }

    public static function schema(): array
    {
        return [
            'label' => [
                'label' => 'Label',
                'type' => 'text',
                'default' => 'Button text'
            ],
            'url' => [
                'label' => 'URL',
                'type' => 'text',
            ],
            'variant' => [
                'label' => 'Variant (set this overwrite all variables)',
                'type' => 'select',
                'options' => [
                    'solid',
                    'outline',
                    'ghost'
                ],
                'default' => 'solid',
            ],
            'size' => [
                'label' => 'Size',
                'type' => 'select',
                'options' => [
                    'xs',
                    'sm',
                    'md',
                    'lg',
                    'xl',
                ],
                'default' => 'md',
            ],
            'bg_color' => [
                'label' => 'Background Color',
                'type' => 'color',
                'default' => '#ffffff',
            ],
            'bg_transparency' => [
                'label' => 'Background Transparency',
                'type' => 'bool',
                'default' => false,
            ],
            'text_color' => [
                'label' => 'Text Color',
                'type' => 'color',
                'default' => '#000000',
            ],
            'border' => [
                'label' => 'Border',
                'type' => 'int',
                'default' => 0,
            ],
            'border_color' => [
                'label' => 'Border Color',
                'type' => 'color',
                'default' => '#000000',
            ],
            'active_hovered_bg_color' => [
                'label' => 'Active Hover Background Color',
                'type' => 'bool',
                'default' => false,
            ],
            'hover_bg_color' => [
                'label' => 'Hover Background Color',
                'type' => 'color',
                'default' => '#000000',
            ],
            'active_hovered_border_color' => [
                'label' => 'Active Hovered Border Color',
                'type' => 'bool',
                'default' => false,
            ],
            'hover_border_color' => [
                'label' => 'Hover Border Color',
                'type' => 'color',
                'default' => '#000000',
            ],
            'active_hovered_text_color' => [
                'label' => 'Active Hovered Text Color',
                'type' => 'bool',
                'default' => false,
            ],
            'hover_text_color' => [
                'label' => 'Hover Text Color',
                'type' => 'color',
                'default' => '#fff',
            ]
        ];
    }

    public static function type(): string
    {
        return 'button';
    }
}
