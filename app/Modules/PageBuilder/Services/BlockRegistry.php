<?php

namespace App\Modules\PageBuilder\Services;

use App\Modules\PageBuilder\Contracts\AbstractBlock;
use InvalidArgumentException;

class BlockRegistry
{
    protected array $blocks = [];

    public function registerMany(array $blockClasses): static
    {
        foreach ($blockClasses as $blockClass) {
            $this->register($blockClass);
        }

        return $this;
    }

    public function register(string $blockClass): static
    {
        if (!is_subclass_of($blockClass, AbstractBlock::class)) {
            throw new InvalidArgumentException("Index class should implement " . AbstractBlock::class);
        }

        $type = $blockClass::type();
        $this->blocks[$type] = $blockClass;

        return $this;
    }

    public function has(string $type): bool
    {
        return isset($this->blocks[$type]);
    }

    public function all(): array
    {
        return $this->blocks;
    }

    public function definitions(): array
    {
        return array_map(fn($class) => $this->definition($class), $this->blocks);
    }

    public function definition(string $class): array
    {
        if (!is_subclass_of($class, AbstractBlock::class)) {
            throw new InvalidArgumentException("Index class should implement " . AbstractBlock::class);
        }

        return [
            'type' => $class::type(),
            'label' => $class::label(),
            'icon' => $class::icon(),
            'schema' => $class::schema()
        ];
    }
}
