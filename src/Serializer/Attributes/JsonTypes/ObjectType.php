<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;

final class ObjectType extends JsonType
{
    public function getPhpType(): string
    {
        return 'object';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['object', 'array'];
    }

    final protected function cast($value): object
    {
        return (object) $value;
    }
}
