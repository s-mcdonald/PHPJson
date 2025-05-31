<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Encoding\Components;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\JsonFormat;
use SamMcDonald\Json\Serializer\Encoding\Components\ArrayToJsonEncoder;
use SamMcDonald\Json\Serializer\Encoding\JsonEncodingResult;

class ArrayToJsonEncoderTest extends TestCase
{
    public function testEncodeReturnsValidJsonObjectWithPrettyFormat(): void
    {
        $encoder = new ArrayToJsonEncoder();

        $array = [
            'name' => 'John Doe',
            'age' => 30,
            'isActive' => true,
        ];

        $result = $encoder->encode($array, JsonFormat::Pretty);
        $expectedJson = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);

        static::assertInstanceOf(JsonEncodingResult::class, $result);
        static::assertTrue($result->isValid());
        static::assertSame($expectedJson, $result->getBody());
    }

    public function testEncodeReturnsValidJsonObjectWithoutPrettyFormat(): void
    {
        $encoder = new ArrayToJsonEncoder();

        $array = [
            'name' => 'John Doe',
            'age' => 30,
            'isActive' => true,
        ];

        $result = $encoder->encode($array, JsonFormat::Compressed);

        $this->assertInstanceOf(JsonEncodingResult::class, $result);
        $this->assertTrue($result->isValid());
        $expectedJson = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        $this->assertSame($expectedJson, $result->getBody());
    }

    public function testEncodeHandlesEncodingErrors(): void
    {
        $encoder = new ArrayToJsonEncoder();

        $array = ["invalid_char" => "\xB1\x31"];

        $result = $encoder->encode($array, JsonFormat::Compressed);

        $this->assertInstanceOf(JsonEncodingResult::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getBody());
    }
}
