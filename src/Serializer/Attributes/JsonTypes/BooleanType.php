<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class BooleanType extends JsonType
{
    public function getPhpType(): string
    {
        return 'boolean';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['boolean', 'integer', 'string', 'NULL'];
    }

    final protected function cast($value): bool
    {
        return (bool) $value;
    }
}
