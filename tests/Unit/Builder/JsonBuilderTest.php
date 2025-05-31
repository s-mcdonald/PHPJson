<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Builder;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Exceptions\JsonException;

#[CoversClass(JsonBuilder::class)]
class JsonBuilderTest extends TestCase
{
    public function testJsonEncodeThrowsJsonException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Recursion error: Does not support itself');
        $jsonBuilder = new JsonBuilder();
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

        $sut = JsonBuilder::createFromJson($original);

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
            (string)JsonBuilder::createFromJson('{"foo": null}')
        );
    }

    public function testCreateFromJsonThrowsJsonException(): void
    {
        $this->expectException(JsonException::class);

        JsonBuilder::createFromJson('{"foo" null}');
    }

    public function testAddNullProperty(): void
    {
        $sut = $this->createBuilder();
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
        $sut = $this->createBuilder();
        $expected = <<<JSON
{
    "foo": {
        "abc": "def"
    }
}
JSON;

        $sut->addProperty(
            "foo",
            $this->createBuilder()->addProperty("abc", "def")
        );

        self::assertEquals(
            $expected,
            ((string) $sut)
        );
    }

    public function testAddArrayProperty(): void
    {
        $sut = $this->createBuilder();

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
        $sut = $this->createBuilder();

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
        $sut = $this->createBuilder();

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
        $sut = $this->createBuilder();

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
        $builder = new JsonBuilder();

        $builder->addProperty('name', 'John Doe');
        $builder->addProperty('age', 30);
        $builder->addProperty('isActive', true);

        $expected = [
            'name' => 'John Doe',
            'age' => 30,
            'isActive' => true,
        ];

        static::assertSame($expected, $builder->toArray());
    }

    public function testBuild(): void
    {
        $sut = $this->createBuilder();

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
