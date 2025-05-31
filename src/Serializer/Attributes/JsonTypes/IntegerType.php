<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class IntegerType extends JsonType
{
    public function getPhpType(): string
    {
        return 'integer';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['double', 'integer', 'string', 'boolean'];
    }

    protected function cast($value): int
    {
        return (int) $value;
    }
}
