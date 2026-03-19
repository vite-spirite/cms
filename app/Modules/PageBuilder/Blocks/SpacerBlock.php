<?php

namespace App\Modules\PageBuilder\Blocks;

class SpacerBlock extends \App\Modules\PageBuilder\Contracts\AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-between-horizontal-start';
    }

    public static function label(): string
    {
        return 'Spacer';
    }

    public static function schema(): array
    {
        return [
            'height' => [
                'label' => 'Spacer',
                'type' => 'int',
                'default' => 1
            ]
        ];
    }

    public static function type(): string
    {
        return 'spacer';
    }
}
