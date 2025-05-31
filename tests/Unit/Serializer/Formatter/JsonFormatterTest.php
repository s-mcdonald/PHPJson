<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Formatter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Serializer\Formatter\JsonFormatter;

#[CoversClass(JsonFormatter::class)]
class JsonFormatterTest extends TestCase
{
    public function testUglify(): void
    {
        $sut = new JsonFormatter();

        $jsonUgly = '{"name":"bar"}';

        $jsonPretty = <<<JSON
{
    "name": "bar"
}
JSON
        ;

        static::assertEquals(
            $jsonUgly,
            $sut->ugly($jsonPretty),
        );
    }
}
