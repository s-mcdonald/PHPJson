<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Assertion;

use JsonException;
use SamMcDonald\Json\Json;
use SamMcDonald\Json\Serializer\Encoding\Validator\JsonValidator;

class JsonAsserter extends AbstractJsonAsserter
{
    private function __construct()
    {
    }

    /**
     * @throws JsonException
     */
    public static function assertStringIsValidJson(string $json, string|null $message = null): void
    {
        if (!(new JsonValidator())->isValid($json)) {
            static::throwInvalidArgument(
                $message ?? 'Expected valid Json. Received Invalid Json structure',
            );
        }
    }

    /**
     * @throws JsonException
     */
    public static function assertJsonHasProperty(string $json, string $property, string|null $message = null): void
    {
        self::assertStringIsValidJson($json);

        if (!array_key_exists($property, Json::convertToArray($json))) {
            static::throwInvalidArgument(
                $message ?? 'Property does not exist on the json value.',
            );
        }
    }

    /**
     * @throws JsonException
     */
    public static function assertJsonPropertyMatchesValue(string $json, string $property, mixed $value, string|null $message = null): void
    {
        self::assertStringIsValidJson($json);
        self::assertJsonHasProperty($json, $property);

        $array = Json::convertToArray($json);
        if (!array_key_exists($property, $array)) {
            static::throwInvalidArgument(
                $message ?? "Can not proceed to assert that property '{$property}' exists on the json value.",
            );
        }

        if ($array[$property] !== $value) {
            static::throwInvalidArgument("The property '{$property}' does not match the expected value '{$value}'.");
        }
    }
}
