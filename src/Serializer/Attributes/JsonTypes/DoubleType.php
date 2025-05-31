<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class DoubleType extends JsonType
{
    public function getPhpType(): string
    {
        return 'double';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['double', 'integer', 'string', 'boolean'];
    }

    final protected function cast($value): float
    {
        return (float) $value;
    }
}
