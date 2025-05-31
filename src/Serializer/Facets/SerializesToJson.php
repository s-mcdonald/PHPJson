<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Facets;

use SamMcDonald\Json\JsonFormat;
use SamMcDonald\Json\Serializer\Attributes\AttributeReader\JsonPropertyReader;
use SamMcDonald\Json\Serializer\JsonSerializer;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\ArrayNormalizer;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\EntityNormalizer;

trait SerializesToJson
{
    protected function serializeToJson(array|null $mapping = null): string
    {
        if (null === $mapping) {
            return self::_toJson($this);
        }

        $arrayToNormalize = [];
        foreach ($mapping as $propName) {
            if (property_exists($this, $propName)) {
                $arrayToNormalize[$propName] = $this->{$propName};
            }
        }

        return self::_toJsonWithMapping($arrayToNormalize);
    }

    final protected static function _toJson(self $self): string
    {
        return (new JsonSerializer(objectNormalizer: new EntityNormalizer(new JsonPropertyReader())))
            ->serialize($self, JsonFormat::Pretty);
    }

    final protected static function _toJsonWithMapping(array|null $mapping = null): string
    {
        return (new JsonSerializer(objectNormalizer: new ArrayNormalizer()))->serialize($mapping, JsonFormat::Pretty);
    }
}
