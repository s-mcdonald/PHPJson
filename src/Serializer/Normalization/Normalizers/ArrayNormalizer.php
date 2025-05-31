<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Normalization\Normalizers;

use InvalidArgumentException;
use JsonException;
use SamMcDonald\Json\Json;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Exceptions\JsonSerializableException;
use SamMcDonald\Json\Serializer\Normalization\Normalizers\Contracts\AbstractNormalizer;
use stdClass;

/**
 * Normalize from array to stdClass.
 */
class ArrayNormalizer extends AbstractNormalizer
{
    public function __construct()
    {
    }

    /**
     * @throws JsonException
     */
    public function normalize(mixed $input): stdClass
    {
        if (false === is_array($input)) {
            throw new InvalidArgumentException('input must be an array.');
        }

        return json_decode(json_encode($input, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
    }

    private function processProperty(string $property, $value, JsonBuilder $jsonBuilder): void
    {
        if (false === $this->canValueSerializable($value)) {
            return;
        }

        $this->assignToStdClass($property, $value, $jsonBuilder);
    }

    private function assignToStdClass($propertyName, $propertyValue, JsonBuilder $classObject): void
    {
        match (\gettype($propertyValue)) {
            'object' => $classObject->addProperty($propertyName, $this->mapObjectContents($propertyValue)),
            'array' => $classObject->addProperty($propertyName, $this->mapArrayContents($propertyValue)),
            'NULL', 'boolean', 'string', 'integer', 'double' => $classObject->addProperty($propertyName, $propertyValue),
            default => throw new JsonSerializableException('Invalid type: Got :' . \gettype($propertyValue)),
        };
    }

    private function mapArrayContents(array $array): array
    {
        $newArray = [];
        foreach ($array as $value) {
            $newArray[] = match (true) {
                is_null($value) => null,
                is_bool($value), is_scalar($value) => $value,
                is_array($value) => $this->mapArrayContents($value),
                default => throw new JsonSerializableException('Invalid type in array.'),
            };
        }

        return $newArray;
    }

    private function mapObjectContents(object $propertyValue): JsonBuilder
    {
        $jsonBuilder = Json::createJsonBuilder();

        foreach ($propertyValue as $key => $value) {
            $this->processProperty($key, $value, $jsonBuilder);
        }

        return $jsonBuilder;
    }
}
