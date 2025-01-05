<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Transformer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Serializer\Transformer\JsonUtilities;

#[CoversClass(JsonUtilities::class)]
class JsonUtilitiesTest extends TestCase
{
    public function testPrettify(): void
    {
        $json = '{"key":"value"}';
        $prettyJson = "{
    \"key\": \"value\"
}";

        static::assertEquals(
            $prettyJson,
            (new JsonUtilities())->prettify($json)
        );
    }

    public function testUglify(): void
    {
        $json = "{
    \"key\": \"value\"
}";
        $uglyJson = '{"key":"value"}';

        static::assertEquals(
            $uglyJson,
            (new JsonUtilities())->uglify($json)
        );
    }

    public function testIsValidWithTrue(): void
    {
        $json = '{"key":"value"}';

        static::assertTrue(
            (new JsonUtilities())->isValid($json)
        );
    }

    public function testIsValidWithFalse(): void
    {
        $json = '{"key":"value"';

        static::assertFalse(
            (new JsonUtilities())->isValid($json)
        );
    }

    public function testPush(): void
    {
        $json = '{"key":"value"}';
        $key = "newKey";
        $item = "newValue";
        $expectedJson = <<<JSON
{
    "key": "value",
    "newKey": "newValue"
}
JSON
            ;

        static::assertEquals(
            $expectedJson,
            (new JsonUtilities())->push($json, $key, $item)
        );
    }

    public function testRemove(): void
    {
        $json = '{"key":"value","toRemove":"value"}';
        $property = "toRemove";
        $expectedJson = <<<JSON
{
    "key": "value"
}
JSON
            ;

        static::assertEquals(
            $expectedJson,
            (new JsonUtilities())->remove($json, $property)
        );
    }
}
