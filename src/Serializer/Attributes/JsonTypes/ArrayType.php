<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class ArrayType extends JsonType
{
    public function getPhpType(): string
    {
        return 'array';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['array', 'object'];
    }

    final protected function cast($value): array
    {
        return (array) $value;
    }
}
