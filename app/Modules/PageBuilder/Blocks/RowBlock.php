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
            'space_x' => [
                'label' => 'Space X',
                'type' => 'int',
                'default' => 2,
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
