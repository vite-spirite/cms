<?php

namespace App\Modules\PageBuilder\Contracts;

interface BlockInterface
{
    public static function type(): string;

    public static function icon(): string;

    public static function label(): string;

    public static function schema(): array;


    public function validate(array $data): array;

    public function toArray(): array;
}
