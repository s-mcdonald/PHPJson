<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class JsonProperty
{
    public function __construct(
        private string|null $name = null,
        private bool $deserialize = false,
    ) {
    }
}
