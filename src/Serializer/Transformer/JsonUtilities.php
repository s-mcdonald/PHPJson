<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Transformer;

use SamMcDonald\Json\Serializer\Encoding\Components\ArrayToJsonEncoder;
use SamMcDonald\Json\Serializer\Encoding\Components\JsonToArrayDecoder;
use SamMcDonald\Json\Serializer\Formatter\JsonFormatter;

/**
 * @deprecated
 */
class JsonUtilities
{
    public function uglify(string $json): string
    {
        return (new JsonFormatter())->ugly($json);
    }

    public function isValid(string $json): bool
    {
        return (new JsonToArrayDecoder())->decode($json)->isValid();
    }

    public function push(string $json, string $key, mixed $item): string|false
    {
        $package = (new JsonToArrayDecoder())->decode($json);
        if (false === $package->isValid()) {
            return false;
        }
        $decodedData = $package->getBody();
        $decodedData[$key] = $item;

        $package = (new ArrayToJsonEncoder())->encode($decodedData);

        return $package->getBody();
    }

    public function remove(string $json, string $property): string|false
    {
        $package = (new JsonToArrayDecoder())->decode($json);
        if (false === $package->isValid()) {
            return false;
        }

        $decodedData = $package->getBody();

        unset($decodedData[$property]);

        $package = (new ArrayToJsonEncoder())->encode($decodedData);

        return $package->getBody();
    }

    public function toArray(string $json): array|false
    {
        $package = (new JsonToArrayDecoder())->decode($json);
        if ($package->isValid()) {
            return $package->getBody();
        }

        return false;
    }
}
