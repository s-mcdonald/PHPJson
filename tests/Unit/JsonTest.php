<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Json;
use SamMcDonald\Json\JsonBuilder;
use SamMcDonald\Json\Serializer\Attributes\JsonProperty;
use SamMcDonald\Json\Serializer\Enums\JsonFormat;
use SamMcDonald\Json\Serializer\Exceptions\JsonSerializableException;
use SamMcDonald\Json\Serializer\Formatter\JsonFormatter;
use SamMcDonald\Json\Serializer\JsonSerializer;
use SamMcDonald\Json\Serializer\Transformer\JsonUtilities;
use SamMcDonald\Json\Tests\Fixtures\Entities\ClassWithMethodAndConstructor;
use SamMcDonald\Json\Tests\Fixtures\Entities\ClassWithPrivateStringProperty;
use SamMcDonald\Json\Tests\Fixtures\Entities\ClassWithPublicStringProperty;
use SamMcDonald\Json\Tests\Fixtures\Entities\GoodChildObjectSerializable;
use SamMcDonald\Json\Tests\Fixtures\Entities\NestingClasses\Nestable;
use SamMcDonald\Json\Tests\Fixtures\Entities\NestingClasses\NestableWithArray;
use SamMcDonald\Json\Tests\Fixtures\Entities\ParentClassSerializable;
use SamMcDonald\Json\Tests\Fixtures\Entities\SimplePropertiesNoOverrideClass;
use SamMcDonald\Json\Tests\Fixtures\Entities\SimplePropertyClass;
use SamMcDonald\Json\Tests\Fixtures\Enums\MyBackedEnum;
use SamMcDonald\Json\Tests\Fixtures\Enums\MyEnum;

#[CoversClass(Json::class)]
#[UsesClass(ParentClassSerializable::class)]
#[UsesClass(GoodChildObjectSerializable::class)]
#[UsesClass(JsonSerializer::class)]
#[UsesClass(JsonBuilder::class)]
#[UsesClass(JsonFormatter::class)]
#[UsesClass(JsonUtilities::class)]

class JsonTest extends TestCase
{
    public function testForbiddenConstruction(): void
    {
        $sut = Json::createFromString('{"foo":"bar"}');
        static::assertInstanceOf(Json::class, $sut);
    }

    public function testSerializeWithBasicNestingClass(): void
    {
        $sut = new ParentClassSerializable(123, '123 Fake Address');
        $sut->name = 'foo';
        $sut->phoneNumbers = [1234, 5678];
        $sut->child = new GoodChildObjectSerializable("fubar");

        $expectedJson = <<<JSON
{
    "userName": "foo",
    "phoneNumbers": [
        1234,
        5678
    ],
    "child": {
        "childProp1": "fubar",
        "childProp2": null
    },
    "userAddress": "123 Fake Address",
    "creditCard": 123
}
JSON
        ;

        static::assertEquals(
            $expectedJson,
            Json::serialize($sut, JsonFormat::Pretty),
        );
    }

    public function testSerializeWithNestingClassWithinNestedClass(): void
    {
        $sut = new ParentClassSerializable(123, '123 Fake Address');
        $sut->name = 'foo';
        $sut->phoneNumbers = [1234, 5678];
        $sut->child = new GoodChildObjectSerializable("fubar");
        $sut->child->childProperty2 = new GoodChildObjectSerializable("deep in the woods");

        $expectedJson = <<<JSON
{
    "userName": "foo",
    "phoneNumbers": [
        1234,
        5678
    ],
    "child": {
        "childProp1": "fubar",
        "childProp2": {
            "childProp1": "deep in the woods",
            "childProp2": null
        }
    },
    "userAddress": "123 Fake Address",
    "creditCard": 123
}
JSON
        ;

        static::assertEquals(
            $expectedJson,
            Json::serialize($sut, JsonFormat::Pretty),
        );
    }

    public function testSerializeWithDeepNestingClassesWithinNestedClass(): void
    {
        $sut = new Nestable();

        $sut->objVal = new Nestable();
        $sut->objVal->intVal = 456;
        $sut->objVal->objVal = new Nestable();
        $sut->objVal->objVal->objVal = new Nestable();
        $sut->objVal->objVal->intVal = 999;
        $sut->objVal->objVal->objVal->objVal = new Nestable();

        $expectedJson = <<<JSON
{
    "intVal": 123,
    "stringVal": "foo",
    "objVal": {
        "intVal": 456,
        "stringVal": "foo",
        "objVal": {
            "intVal": 999,
            "stringVal": "foo",
            "objVal": {
                "intVal": 123,
                "stringVal": "foo",
                "objVal": {
                    "intVal": 123,
                    "stringVal": "foo",
                    "objVal": null
                }
            }
        }
    }
}
JSON
        ;

        static::assertEquals(
            $expectedJson,
            Json::serialize($sut, JsonFormat::Pretty),
        );
    }

    public function testSerializeWithArray(): void
    {
        $sut = new NestableWithArray();

        $sut->arrayVal = [false, true, null, 1, 2, 3, ["a", "b"]];

        $expectedJson = <<<JSON
{
    "arrayVal": [
        false,
        true,
        null,
        1,
        2,
        3,
        [
            "a",
            "b"
        ]
    ],
    "intVal": 123,
    "stringVal": "foo",
    "objVal": null
}
JSON
        ;

        static::assertEquals(
            $expectedJson,
            Json::serialize($sut, JsonFormat::Pretty),
        );
    }

    public function testSerializationNestedObjectInArrays(): void
    {
        $sut = new NestableWithArray();

        $nestA = new Nestable();
        $nestA->stringVal = "arrayitem";

        $sut->arrayVal = [$nestA, 1, "fubar", true];

        $sut->objVal = new Nestable();
        $sut->objVal->intVal = 456;
        $sut->objVal->objVal = new Nestable();
        $sut->objVal->objVal->objVal = new Nestable();

        $expectedJson = <<<JSON
{
    "arrayVal": [
        {
            "intVal": 123,
            "stringVal": "arrayitem",
            "objVal": null
        },
        1,
        "fubar",
        true
    ],
    "intVal": 123,
    "stringVal": "foo",
    "objVal": {
        "intVal": 456,
        "stringVal": "foo",
        "objVal": {
            "intVal": 123,
            "stringVal": "foo",
            "objVal": {
                "intVal": 123,
                "stringVal": "foo",
                "objVal": null
            }
        }
    }
}
JSON
        ;

        static::assertEquals(
            $expectedJson,
            Json::serialize($sut, JsonFormat::Pretty),
        );
    }

    #[DataProvider('provideDataForBadPropertyName')]
    public function testSerializeWithBadPropertyNameCausesException($badPropertyNameObject): void
    {
        $this->expectException(InvalidArgumentException::class);

        $sut = $badPropertyNameObject;
        $sut->badProperty = 'foo';

        static::assertEquals(
            '{"name":"foo"}',
            Json::serialize($sut),
        );
    }

    public static function provideDataForBadPropertyName(): array
    {
        return [
            'space in property name' => [
                new class
                {
                    #[JsonProperty('user Name')]
                    public string $badProperty;
                }
            ],
            'minus in property name' => [
                new class
                {
                    #[JsonProperty('-')]
                    public string $badProperty;
                }
            ],
            'equal sign in property name' => [
                new class
                {
                    #[JsonProperty('=')]
                    public string $badProperty;
                }
            ],
        ];
    }

    public function testSerializeWithConstructorPrivatePropertySerialized(): void
    {
        $sut = new ClassWithMethodAndConstructor(1234);
        $sut->name = 'foo';
        $sut->phoneNumbers = ['1234', '5678'];

        static::assertEquals(
            '{"userName":"foo","phoneNumbers":["1234","5678"],"creditCard":1234}',
            Json::serialize($sut),
        );
    }

    public function testDeserializeHydration(): void
    {
        $expected = new SimplePropertiesNoOverrideClass('foo-name', 44);

        $json = <<<JSON
{
  "name": "foo-name", 
  "age": 44
}
JSON
        ;

        $hydrated = Json::deserialize($json, SimplePropertiesNoOverrideClass::class);
        assert($hydrated instanceof SimplePropertiesNoOverrideClass);

        static::assertEquals(
            $expected,
            $hydrated,
        );

        static::assertEquals(
            'foo-name',
            $hydrated->getName(),
        );

        static::assertEquals(
            44,
            $hydrated->getAge(),
        );
    }

    public function testDeserializeHydrationWithPropertyOverride(): void
    {
        $expected = new SimplePropertyClass("myusername", 44);

        $json = <<<JSON
{
  "userName": "myusername", 
  "age": 44
}
JSON
        ;

        $hydrated = Json::deserialize($json, SimplePropertyClass::class);
        assert($hydrated instanceof SimplePropertyClass);

        static::assertEquals(
            $expected,
            $hydrated,
        );

        static::assertEquals(
            'myusername',
            $hydrated->getName(),
        );

        static::assertEquals(
            44,
            $hydrated->getAge(),
        );
    }

    /**
     * This test SHOULD throw an exception, as we have explicitly declared that a specific
     * property should be serialized. If the property is not annotated with
     * JsonProperty, it would otherwise proceed silently.
     */
    public function testSerializeWithUninitializedPropertyCausesException(): void
    {
        $this->expectException(JsonSerializableException::class);
        $this->expectExceptionMessage('Value not initialized');

        $sut = new ClassWithPublicStringProperty();

        Json::serialize($sut);
    }

    public function testSimpleSerializationOfPublicStringProperty(): void
    {
        $sut = new ClassWithPublicStringProperty();
        $sut->name = 'bar';

        static::assertEquals(
            '{"name":"bar"}',
            Json::serialize($sut),
        );
    }

    public function testSimpleSerializationOfPrivateStringProperty(): void
    {
        $sut = new ClassWithPrivateStringProperty('fubar');

        static::assertEquals(
            '{"name":"fubar"}',
            Json::serialize($sut),
        );
    }

    /**
     * This test will compare the serialized result of a BackedEnum.
     * Backed enums are serialized with their backing value.
     */
    public function testSerializeBackedEnumTest(): void
    {
        $foo = MyBackedEnum::Foo;

        static::assertEquals(
            '{"MyBackedEnum":"foo"}',
            Json::serialize($foo),
        );
    }

    /**
     * This test will compare the serialized result of a Pure Enum.
     * Pure enums are serialized with their constant name.
     */
    public function testSerializePureEnumTest(): void
    {
        $foo = MyEnum::foo;

        static::assertEquals(
            '{"MyEnum":"foo"}',
            Json::serialize($foo),
        );
    }

    public function testPretty(): void
    {
        $jsonUgly = '{"name":"bar"}';

        $jsonPretty = <<<JSON
{
    "name": "bar"
}
JSON
        ;

        $sut = Json::createFromString($jsonUgly);

        static::assertEquals(
            $jsonPretty,
            $sut->toPretty(),
        );
    }

    /**
     * Test the formatting on the Json::facade
     */
    public function testJsonPrettify(): void
    {
        $jsonUgly = '{"name":"bar"}';

        $jsonPretty = <<<JSON
{
    "name": "bar"
}
JSON
        ;

        static::assertEquals(
            $jsonPretty,
            Json::prettify($jsonUgly),
        );
    }

    /**
     * Test the formatting on the Json::facade
     */
    public function testUglify(): void
    {
        $jsonUgly = '{"name":"bar"}';

        $jsonPretty = <<<JSON
{
    "name": "bar"
}
JSON
        ;

        static::assertEquals(
            $jsonUgly,
            Json::uglify($jsonPretty),
        );
    }

    #[DataProvider('provideDataForTestJsonIsValid')]
    public function testJsonIsValid(string $json, bool $valid): void
    {
        static::assertEquals(
            $valid,
            Json::isValid($json),
        );
    }

    public static function provideDataForTestJsonIsValid(): array
    {
        return [
            'valid json' => [
                '{"name":"bar"}',
                true,
            ],
            'invalid json' => [
                '{"name":"bar"',
                false,
            ],
        ];
    }

    public function testToArray(): void
    {
        $json = '{"name":"bar","age":19, "isActive":true, "children": [{"name":"child1"},{"name":"child2"}]}';

        $jsonObj = Json::createFromString($json);

        $array = $jsonObj->toArray();

        static::assertIsArray($array);
        static::assertCount(4, $array);
        static::assertEquals('bar', $array['name']);
        static::assertEquals(19, $array['age']);
        static::assertTrue($array['isActive']);
        static::assertIsArray($array['children']);
        static::assertCount(2, $array['children']);
        static::assertEquals('child1', $array['children'][0]['name']);
        static::assertEquals('child2', $array['children'][1]['name']);
    }

    public function testConvertToArray(): void
    {
        $json = '{"name":"bar","age":19, "isActive":true, "children": [{"name":"child1"},{"name":"child2"}]}';

        $array = Json::convertToArray($json);

        static::assertIsArray($array);
        static::assertCount(4, $array);
        static::assertEquals('bar', $array['name']);
        static::assertEquals(19, $array['age']);
        static::assertTrue($array['isActive']);
        static::assertIsArray($array['children']);
        static::assertCount(2, $array['children']);
        static::assertEquals('child1', $array['children'][0]['name']);
        static::assertEquals('child2', $array['children'][1]['name']);
    }

    /**
     * With malformed json string, false is expected to be returned
     */
    public function testConvertToArrayWithBadJson(): void
    {
        $json = '{"name":"bar","age":19, "isActive":true, "children": [{"name":"child1"},{"name":"child2"}';
        $array = Json::convertToArray($json);
        static::assertFalse($array);
    }

    public function testCreateJsonBuilder(): void
    {
        $builder = Json::createJsonBuilder();

        static::assertEquals([], $builder->toArray());
        static::assertEquals(new \stdClass(), $builder->toStdClass());
    }

    #[DataProvider('provideDataForTestPush')]
    public function testPush(string $json, string $expected): void
    {
        static::assertEquals($expected, Json::push($json, "foo", "foovalue"));
    }

    public static function provideDataForTestPush(): \Generator
    {
        yield
            'json1' => [
            '{"key":"value"}',
            <<<JSON
{
    "key": "value",
    "newKey": "newValue"
}
JSON
            ];


        yield
        'json1' => [
            '{"name":"bar","age":19, "isActive":true, "children": [{"name":"child1"},{"name":"child2"}]}',
            <<<JSON
{
    "name": "bar",
    "age": 19,
    "isActive": true,
    "children": [
        {
            "name": "child1"
        },
        {
            "name": "child2"
        }
    ],
    "foo": "foovalue"
}
JSON,
        ];
    }

    public function testPushWithInvalidJson(): void
    {
        $json = '{"key":value"}';
        $key = "newKey";
        $item = "newValue";

        static::assertFalse(
            Json::push($json, $key, $item),
        );
    }

    public function testAddProperty(): void
    {
        $json = '{"name":"bar"}';

        $expected = <<<JSON
{
    "name": "bar",
    "age": 19
}
JSON;
        $sut = Json::createFromString($json);

        static::assertEquals(
            $expected,
            (string)$sut->addProperty("age", 19)->toPretty(),
        );
    }

    public function testRemove(): void
    {
        $json = '{"name":"bar","age":19, "isActive":true, "children": [{"name":"child1"},{"name":"child2"}]}';

        $expected = <<<JSON
{
    "name": "bar",
    "age": 19,
    "isActive": true
}
JSON;

        static::assertEquals($expected, Json::remove($json, "children"));
    }

    public function testRemoveWithValidData(): void
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
            Json::remove($json, $property)
        );
    }

    public function testRemoveWithInvalidJson(): void
    {
        $json = '{"key":value"}';

        static::assertFalse(
            Json::remove($json, 'key'),
        );
    }

    public function testCreateFromFile(): void
    {
        $fileName = __DIR__ . '/../Fixtures/Files/basic.json';
        $json = Json::createFromFile($fileName);

        $expected = '{"foo":"bar","num":123,"double":123.567}';

        static::assertEquals(
            $expected,
            $json->toUgly(),
        );
    }

    public function testToUgly(): void
    {
        $json = "{
    \"key\": \"value\"
}";

        $expected = '{"key":"value"}';

        $json = Json::createFromString($json);

        static::assertEquals(
            $expected,
            $json->toUgly(),
        );
    }

    public function testValidate(): void
    {
        $jsonBad = '{"foo":"bar","num":123,double:123.567}';
        $jsonGood = '{"foo":"bar","num":123,"double":123.567}';

        static::assertTrue(
            Json::validate($jsonGood),
        );
        static::assertTrue(
            json_validate($jsonGood),
        );

        static::assertFalse(
            json_validate($jsonBad),
        );
        static::assertFalse(
            Json::validate($jsonBad),
        );
    }

    public function testIterate(): void
    {
        $foundFoo = false;
        $foundNum = false;
        $foundDouble = false;

        $json = '{"foo":"bar","num":123,"double":123.567}';

        foreach (Json::iterate($json) as $key => $value) {
            echo $key . ' => ' . $value . "\n";
            if ($key === 'foo') {
                $foundFoo = true;
            }
            if ($key === 'num') {
                $foundNum = true;
            }
            if ($key === 'double') {
                $foundDouble = true;
            }
        }

        static::assertTrue($foundFoo);
        static::assertTrue($foundNum);
        static::assertTrue($foundDouble);
    }

    public function testIterateWithInvalidJson(): void
    {
        $this->expectException(JsonSerializableException::class);

        $json = '{"foo":"bar","num:123,"double":123.567}';

        Json::iterate($json);
    }
}
