<?php


namespace App\Modules\PageBuilder\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class RowBlock extends AbstractBlock
{

    public static function icon(): string
    {
        return 'i-lucide-rows-2';
    }

    public static function label(): string
    {
        return 'Row';
    }

    public static function schema(): array
    {
        return [
            'gap' => [
                'label' => 'Spacing',
                'type' => 'int',
                'default' => 2,
            ],
            'align_items' => [
                'label' => 'vertical alignment',
                'type' => 'select',
                'options' => [
                    'start',
                    'center',
                    'end',
                    'stretch',
                ]
            ],
            'justify_content' => [
                'label' => 'Horizontal alignment',
                'type' => 'select',
                'options' => [
                    'start',
                    'center',
                    'end',
                    'between',
                ]
            ],
            'wrap' => [
                'label' => 'Wrap content',
                'type' => 'bool',
                'default' => false,
            ],
            'children' => [
                'label' => 'Children',
                'type' => 'blocks',
                'default' => [],
            ]
        ];
    }

    public static function type(): string
    {
        return 'row';
    }

    public function toArray(): array
    {
        $base = parent::toArray();

        $base['children'] = $this->data['children'] ?? [];
        return $base;
    }
}
