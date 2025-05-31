<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Builder;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Json;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Exceptions\JsonException;
use SamMcDonald\Json\Serializer\JsonSerializer;

#[CoversClass(JsonBuilder::class)]
class JsonBuilderTest extends TestCase
{
    public function testJsonEncodeThrowsJsonException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Recursion error: Does not support itself');
        $jsonBuilder = Json::createJsonBuilder();
        $data = [];
        $data['self'] = $jsonBuilder;
        $jsonBuilder->addProperty('self', $data);
        $jsonBuilder->addProperty('self', $data);
        $jsonBuilder->addProperty('self', $data);

        echo $jsonBuilder;
    }

    public function testRemoveProperty(): void
    {
        $original = <<<JSON
{
    "foo": 12345.678,
    "bar": null
}
JSON;

        $expected = <<<JSON
{
    "foo": 12345.678
}
JSON;

        $sut = JsonBuilder::createFromJson($original, Json::getJsonSerializer());

        self::assertEquals(
            $expected,
            (string)$sut->removeProperty("bar")
        );
    }

    public function testCreateFromJson(): void
    {
        $expected = <<<JSON
{
    "foo": null
}
JSON;
        self::assertEquals(
            $expected,
            (string)JsonBuilder::createFromJson('{"foo": null}', Json::getJsonSerializer())
        );
    }

    public function testCreateFromJsonThrowsJsonException(): void
    {
        $this->expectException(JsonException::class);

        JsonBuilder::createFromJson('{"foo" null}', Json::getJsonSerializer());
    }

    public function testAddNullProperty(): void
    {
        $sut = Json::createJsonBuilder();
        $expected = <<<JSON
{
    "foo": null
}
JSON;

        $sut->addNullProperty("foo");

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddObjectProperty(): void
    {
        $sut = Json::createJsonBuilder();
        $expected = <<<JSON
{
    "foo": {
        "abc": "def"
    }
}
JSON;

        $sut->addProperty(
            "foo",
            Json::createJsonBuilder()->addProperty("abc", "def")
        );

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddArrayProperty(): void
    {
        $sut = Json::createJsonBuilder();

        $expected = <<<JSON
{
    "foo": [
        "bar"
    ]
}
JSON;

        $sut->addProperty("foo", ["bar"]);

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddStringProperty(): void
    {
        $sut = Json::createJsonBuilder();

        $expected = <<<JSON
{
    "foo": "bar"
}
JSON;

        $sut->addProperty("foo", "bar");

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddBooleanProperty(): void
    {
        $sut = Json::createJsonBuilder();

        $expected = <<<JSON
{
    "foo": true
}
JSON;

        $sut->addProperty("foo", true);

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddNumericProperty(): void
    {
        $sut = Json::createJsonBuilder();

        $expected = <<<JSON
{
    "foo": 12345.678
}
JSON;

        $sut->addProperty("foo", 12345.678);

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testToArrayReturnsCorrectArray(): void
    {
        $sut = Json::createJsonBuilder();

        $sut->addProperty('name', 'John Doe');
        $sut->addProperty('age', 30);
        $sut->addProperty('isActive', true);

        $expected = [
            'name' => 'John Doe',
            'age' => 30,
            'isActive' => true,
        ];

        static::assertSame($expected, $sut->toArray());
    }

    public function testBuild(): void
    {
        $sut = Json::createJsonBuilder();

        $expected = <<<JSON
{
    "foo": 12345.678,
    "foo22": 22
}
JSON;

        $sut->addProperty("foo", 12345.678);
        $sut->addProperty("foo22", 22);

        self::assertEquals(
            $expected,
            $sut->build()
        );
    }

    private function createBuilder(): JsonBuilder
    {
        return new JsonBuilder();
    }
}
