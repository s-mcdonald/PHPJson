<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Facets;

trait SerializesToJson
{
    use SerializesWithMapping;

    protected function serializeToJson(array|null $mapping = null): string
    {
        if (null === $mapping) {
            return $this->_toJson();
        }

        $arrayToNormalize = [];
        foreach ($mapping as $propName) {
            if (property_exists($this, $propName)) {
                $arrayToNormalize[$propName] = $this->{$propName};
            }
        }

        return $this->_toJson($arrayToNormalize);
    }
}
