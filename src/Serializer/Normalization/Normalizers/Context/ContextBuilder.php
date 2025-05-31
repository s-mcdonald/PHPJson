<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Normalization\Normalizers\Context;

use ReflectionMethod;
use ReflectionProperty;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Attributes\AttributeReader\JsonPropertyReader;
use SamMcDonald\Json\Serializer\Attributes\JsonProperty;

readonly class ContextBuilder
{
    public function __construct(
        private JsonPropertyReader $propertyReader,
    ) {
    }

    public function build(
        ReflectionProperty|ReflectionMethod $prop,
        object $originalObject,
        JsonBuilder $classObject,
    ): Context {
        $jsonPropertyAttributes = $prop->getAttributes(JsonProperty::class);
        $propertyName = $this->propertyReader->getJsonPropertyName($prop->getName(), $jsonPropertyAttributes);
        $propertyType = $this->propertyReader->getJsonPropertyCastTypeName($jsonPropertyAttributes);

        return new Context(
            $prop,
            $originalObject,
            $classObject,
            $jsonPropertyAttributes,
            $propertyName,
            $propertyType,
        );
    }
}
