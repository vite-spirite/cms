<?php


namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class TextBlock extends AbstractBlock
{
    public static function type(): string
    {
        return 'text';
    }

    public static function label(): string
    {
        return 'Texte riche';
    }

    public static function icon(): string
    {
        return 'i-lucide-text-initial';
    }

    public static function schema(): array
    {
        return [
            'content' => [
                'type' => 'richtext',
                'label' => 'Contenu',
                'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec mauris eget sapien ornare imperdiet. Aliquam erat volutpat. Praesent et vulputate odio, nec malesuada massa. Maecenas ut faucibus est. Vivamus viverra massa ex, non lobortis ligula suscipit id. Sed tempus lacinia fringilla. Fusce a auctor felis. ',
                'required' => true,
            ],
            'align' => [
                'type' => 'select',
                'label' => 'Alignment',
                'default' => 'left',
                'options' => ['left', 'center', 'right', 'justify'],
            ],
        ];
    }
}
