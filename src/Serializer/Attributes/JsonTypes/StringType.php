<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes;

use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\JsonType;
use SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts\TypeCasting;

final class StringType extends JsonType implements TypeCasting
{
    public function getPhpType(): string
    {
        return 'string';
    }

    public function getCompatibleCastTypes(): array
    {
        return ['string', 'integer', 'double', 'boolean', 'NULL'];
    }

    final protected function cast($value): string
    {
        return (string) $value;
    }
}
