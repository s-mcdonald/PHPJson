<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Normalization\Normalizers;

use ReflectionProperty;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\Context\Context;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\Context\ContextBuilder;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\Contracts\AbstractClassNormalizer;

/**
 * Normalize object properties to stdClass. Similar to ObjectNormalizer however
 * this simply takes all properties of an object and does
 * not deal with Attributes or methods.
 */
final class EntityNormalizer extends AbstractClassNormalizer
{
    protected function transferToJsonBuilder(object $propertyValue): JsonBuilder
    {
        $jsonBuilder = new JsonBuilder();
        $contextBuilder = new ContextBuilder($this->propertyReader);

        foreach ($this->getReflectionProperties($propertyValue) as $prop) {
            assert($prop instanceof ReflectionProperty);
            $this->processProperty($contextBuilder->build($prop, $propertyValue, $jsonBuilder));
        }

        return $jsonBuilder;
    }

    protected function processProperty(Context $context): void
    {
        if (false === $context->getReflectionItem()->isInitialized($context->getOriginalObject())) {
            return;
        }

        $propertyValue = $this->getValueFromPropOrMethod($context->getReflectionItem(), $context->getOriginalObject());

        if (false === $this->canValueSerializable($propertyValue)) {
            return;
        }

        $this->assignToStdClass($context->getPropertyName(), $propertyValue, $context->getClassObject());
    }

    protected function processMethod(Context $context): void
    {
    }
}
