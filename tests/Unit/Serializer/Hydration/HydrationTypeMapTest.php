<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Hydration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Serializer\Hydration\HydrationTypeMap;

#[CoversClass(HydrationTypeMap::class)]
class HydrationTypeMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $map = [
            'int' => 'integer',
            'float' => 'double',
            'bool' => 'boolean',
            'string' => 'string',
            'array' => 'array',
            'object' => 'object',
            'null' => 'NULL',
            'resource' => 'resource',
            'mixed' => 'unknown type',
        ];

        static::assertEquals(HydrationTypeMap::TYPE_MAP, $map);
    }

    public function testGet(): void
    {
        static::assertEquals(
            'integer',
            HydrationTypeMap::get('int')
        );

        static::assertEquals(
            'double',
            HydrationTypeMap::get('float')
        );

        static::assertEquals(
            'boolean',
            HydrationTypeMap::get('bool')
        );

        static::assertEquals(
            'string',
            HydrationTypeMap::get('string')
        );

        static::assertEquals(
            'object',
            HydrationTypeMap::get('object')
        );

        static::assertEquals(
            'resource',
            HydrationTypeMap::get('resource')
        );

        static::assertEquals(
            'NULL',
            HydrationTypeMap::get('null')
        );

        static::assertEquals(
            'unknown type',
            HydrationTypeMap::get('mixed')
        );
    }
}
