<?php

namespace App\Core\Navigation\Data;

class NavigationItem
{
    public function __construct(
        public readonly string  $label,
        public readonly ?string $icon = null,
        public readonly ?string $route = null,
        public readonly mixed   $badge = null,
        public readonly bool    $disabled = false,
        /** @var NavigationItem[]|null * */
        public readonly ?array  $children = null,
        public readonly ?string $class = null,
    )
    {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'] ?? throw new \InvalidArgumentException('label is required'),
            icon: $data['icon'] ?? null,
            route: $data['route'] ?? null,
            badge: $data['badge'] ?? null,
            disabled: $data['disabled'] ?? false,
            children: isset($data['children'])
                ? array_map(fn($child) => self::fromArray($child), $data['children'])
                : null,
            class: $data['class'] ?? null);
    }

    public function toArray(): array
    {
        $badge = is_callable($this->badge) ? call_user_func($this->badge) : $this->badge;

        return array_filter([
            'label' => $this->label,
            'icon' => $this->icon,
            'route' => $this->route,
            'badge' => $badge,
            'disabled' => $this->disabled,
            'children' => $this->children ? array_map(fn($child) => $child->toArray(), $this->children) : null,
            'class' => $this->class,
        ], fn($value) => $value !== null);
    }
}
