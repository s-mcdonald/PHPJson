<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Transformer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Serializer\Transformer\JsonUtilities;

#[CoversClass(JsonUtilities::class)]
class JsonUtilitiesTest extends TestCase
{
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

    public function testPushWithInvalidJson(): void
    {
        $json = '{"key":value"}';
        $key = "newKey";
        $item = "newValue";

        static::assertFalse(
            (new JsonUtilities())->push($json, $key, $item),
        );
    }

    public function testRemoveWithInvalidJson(): void
    {
        $json = '{"key":value"}';

        static::assertFalse(
            (new JsonUtilities())->remove($json, 'key'),
        );
    }

    public function testToArray(): void
    {
        $json = '{"key":"value","arr":[1,2,3]}';

        static::assertEquals(
            ['key' => 'value', 'arr' => [1, 2, 3]],
            (new JsonUtilities())->toArray($json),
        );
    }

    public function testToArrayWithInvalid(): void
    {
        $json = '{"key":value"}';

        static::assertFalse(
            (new JsonUtilities())->toArray($json),
        );
    }
}
