<?php

namespace App\Modules\Gallery\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class MediaBlock extends AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-images';
    }

    public static function label(): string
    {
        return 'Media Gallery';
    }

    public static function schema(): array
    {
        return [
            'media' => [
                'label' => 'Media',
                'type' => 'media',
                'default' => 'https://picsum.photos/1080/640',
                'required' => true,
            ],
            'alt' => [
                'label' => 'Alt',
                'type' => 'text',
                'default' => '',
                'required' => false,
            ]
        ];
    }

    public static function type(): string
    {
        return 'media-image';
    }
}
