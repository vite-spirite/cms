<?php


namespace App\Modules\PageBuilder\Contracts;

use InvalidArgumentException;

abstract class AbstractBlock implements BlockInterface
{

    protected string $id;
    protected array $data;
    protected int $order;

    public function __construct(string $id, array $data = [], int $order = 0)
    {
        $this->id = $id;
        $this->data = $data;
        $this->order = $order;
    }

    abstract public static function icon(): string;

    abstract public static function label(): string;

    public function validate(array $data): array
    {
        $schema = static::schema();
        $cleaned = [];

        foreach ($schema as $field => $rules) {
            $value = $data[$field] ?? $rules['default'] ?? null;

            if (($rules['required'] ?? false) && empty($value)) {
                throw new InvalidArgumentException(
                    sprintf('The field “%s” is required for the block “%s.”', $field, static::type())
                );
            }

            $cleaned[$field] = $value;
        }

        return $cleaned;
    }

    abstract public static function schema(): array;

    abstract public static function type(): string;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'order' => $this->order,
            'type' => static::type()
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
