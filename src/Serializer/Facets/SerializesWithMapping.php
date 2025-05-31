<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Facets;

use SamMcDonald\Json\JsonFormat;
use SamMcDonald\Json\Serializer\Attributes\AttributeReader\JsonPropertyReader;
use SamMcDonald\Json\Serializer\JsonSerializer;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\ArrayNormalizer;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\EntityNormalizer;

trait SerializesWithMapping
{
    final protected function _toJson(array|null $mapping = null): string
    {
        if (null === $mapping) {
            $serializer = new JsonSerializer(objectNormalizer: new EntityNormalizer(new JsonPropertyReader()));

            return $serializer->serialize($this, JsonFormat::Pretty);
        }

        $serializer = new JsonSerializer(objectNormalizer: new ArrayNormalizer());

        return $serializer->serialize($mapping, JsonFormat::Pretty);
    }
}
