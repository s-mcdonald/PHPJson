<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

class IntegerType extends JsonType
{
    public function getPhpType(): string
    {
        return 'integer';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['double', 'integer', 'string', 'boolean'];
    }

    final protected function cast($value): int
    {
        return (int) $value;
    }
}
