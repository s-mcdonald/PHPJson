<?php

declare(strict_types=1);

namespace SamMcDonald\Json;

use ArrayIterator;
use SamMcDonald\Json\Loaders\LocalFileLoader;
use SamMcDonald\Json\Loaders\UrlLoader;
use SamMcDonald\Json\Serializer\Encoding\Components\ArrayToJsonEncoder;
use SamMcDonald\Json\Serializer\Encoding\Components\JsonToArrayDecoder;
use SamMcDonald\Json\Serializer\Exceptions\JsonSerializableException;
use SamMcDonald\Json\Serializer\JsonSerializer;

final class Json
{
    private static JsonSerializer|null $jsonSerializer = null;

    private array $jsonProperties;

    private function __construct(string $json)
    {
        $this->jsonProperties = self::convertToArray($json);
    }

    public function toPretty(): string
    {
        return (new ArrayToJsonEncoder())->encode($this->jsonProperties)->getBody();
    }

    public function toUgly(): string
    {
        return (new ArrayToJsonEncoder())->encode($this->jsonProperties, JsonFormat::Compressed)->getBody();
    }

    public function toArray(): array|false
    {
        return $this->jsonProperties;
    }

    public function addProperty(string $key, mixed $value): self
    {
        $this->jsonProperties[$key] = $value;

        return $this;
    }

    public static function createFromString(string $json): self
    {
        return new self($json);
    }

    public static function createFromFile(string $fileName): self
    {
        return new self((new LocalFileLoader())->load($fileName));
    }

    public static function createFromUrl(string $url): self
    {
        return new self((new UrlLoader())->load($url));
    }

    public static function serialize(
        object $object,
        JsonFormat $format = JsonFormat::Compressed,
    ): string {
        return self::getJsonSerializer()->serialize($object, $format);
    }

    public static function deserialize(string $json, string $classFqn): mixed
    {
        return self::getJsonSerializer()->deserialize($json, $classFqn);
    }

    public static function createJsonBuilder(): JsonBuilder
    {
        return new JsonBuilder(self::getJsonSerializer());
    }

    public static function prettify(string $json): string
    {
        return json_encode(json_decode($json, false), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    public static function uglify(string $json): string
    {
        return json_encode(json_decode($json, false), JSON_THROW_ON_ERROR);
    }

    public static function isValid(string $json): bool
    {
        return (new JsonToArrayDecoder())->decode($json)->isValid();
    }

    public static function push(string $json, string $key, mixed $item): string|false
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

    public static function remove(string $json, string $property): string|false
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

    public static function convertToArray(string $json): array|false
    {
        $package = (new JsonToArrayDecoder())->decode($json);
        if ($package->isValid()) {
            return $package->getBody();
        }

        return false;
    }

    public static function validate(string $json): bool
    {
        return json_validate($json);
    }

    public static function iterate(string $json): ArrayIterator
    {
        $decoded = (new JsonToArrayDecoder())->decode($json);

        if (false === $decoded->isValid()) {
            throw JsonSerializableException::unableToDecode();
        }

        return new ArrayIterator($decoded->getBody());
    }

    public static function getJsonSerializer(): JsonSerializer
    {
        if (null === self::$jsonSerializer) {
            self::$jsonSerializer = new JsonSerializer();
        }

        return self::$jsonSerializer;
    }
}
