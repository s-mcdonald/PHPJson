<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class NullType extends JsonType
{
    public function getPhpType(): string
    {
        return 'NULL';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['NULL', 'boolean'];
    }

    protected function cast($value): null
    {
        return null;
    }
}
