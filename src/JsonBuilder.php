<?php

declare(strict_types=1);

namespace SamMcDonald\Json;

use InvalidArgumentException;

use SamMcDonald\Json\Exceptions\JsonBuilderException;
use SamMcDonald\Json\Serializer\Encoding\Components\JsonToStdClassDecoder;
use SamMcDonald\Json\Serializer\Exceptions\JsonException;
use stdClass;
use Throwable;

final class JsonBuilder
{
    private array $jsonProperties = [];

    public function __toString(): string
    {
        try {
            return json_encode($this->jsonProperties, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } catch (Throwable $t) {
            throw JsonBuilderException::createWithMessage($t->getMessage());
        }
    }

    public function addNullProperty(string $prop): self
    {
        self::assertPropertyName($prop);

        return $this->addProperty($prop, null);
    }

    public static function createFromJson(string $json): self
    {
        if (false === Json::isValid($json)) {
            throw new JsonException('Invalid source Json');
        }

        $builder = new self();
        foreach (Json::toArray($json) as $prop => $value) {
            $builder->addProperty($prop, $value);
        }

        return $builder;
    }

    public function addProperty(string $prop, mixed $value): self
    {
        self::assertPropertyName($prop);

        return $this->addProp($prop, self::cleanValue($value, $this));
    }

    public function toStdClass(): stdClass
    {
        try {
            return (new JsonToStdClassDecoder())->decode((string) $this)->getBody();
        } catch (Throwable $t) {
            throw JsonBuilderException::createWithMessage($t->getMessage());
        }
    }

    public function toArray(): array
    {
        try {
            return json_decode((string) $this, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $t) {
            throw JsonBuilderException::createWithMessage($t->getMessage());
        }
    }

    public function build(): string
    {
        return (string) $this;
    }

    public function removeProperty(string $prop): self
    {
        unset($this->jsonProperties[$prop]);

        return $this;
    }

    protected static function assertPropertyName(string $prop): void
    {
        if (!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9-_]*$/', $prop)) {
            throw new InvalidArgumentException('Invalid property name');
        }
    }

    protected static function cleanValue(mixed $value, self $instance): mixed
    {
        if ($value instanceof self) {
            return $value->toStdClass();
        }

        if (is_array($value)) {
            $value = self::cleanArrayValue($value, $instance);
        }

        if (is_object($value)) {
            throw new InvalidArgumentException('Does not support Object types.');
        }

        return match (\gettype($value)) {
            'array', 'string', 'double', 'integer', 'boolean', 'NULL' => $value,
            default => throw new InvalidArgumentException('Invalid value type - Received : ' . gettype($value)),
        };
    }

    protected static function cleanArrayValue(array $value, self $instance): array
    {
        $returnArray = [];

        foreach ($value as $key => $val) {
            if ($val === $instance) {
                throw new InvalidArgumentException('Recursion error: Does not support itself');
            }
            if (is_array($val)) {
                $returnArray[$key] = self::cleanArrayValue($val, $instance);
                continue;
            }

            $returnArray[$key] = self::cleanValue($val, $instance);
        }

        return $returnArray;
    }

    private function addProp(string $prop, mixed $value = null): self
    {
        $this->jsonProperties[$prop] = $value;

        return $this;
    }
}
