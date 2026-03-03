<?php

namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class ImageBlock extends AbstractBlock
{
    public static function type(): string
    {
        return 'image';
    }

    public static function icon(): string
    {
        return 'i-lucide-image';
    }

    public static function label(): string
    {
        return 'Image';
    }

    public static function schema(): array
    {
        return [
            'url' => [
                'type' => 'text',
                'label' => 'URL',
                'default' => 'https://picsum.photos/1080/640',
                'required' => true,
            ],
            'alt' => [
                'type' => 'text',
                'label' => 'Alt',
                'default' => 'Random sample image',
                'required' => true
            ]
        ];
    }
}
