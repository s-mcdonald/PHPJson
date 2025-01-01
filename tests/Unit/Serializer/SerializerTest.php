<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Json;
use SamMcDonald\Json\Tests\Unit\Serializer\Fixtures\BadPropertyNamesSerializable;
use SamMcDonald\Json\Tests\Unit\Serializer\Fixtures\ParentClassSerializable;

class SerializerTest extends TestCase
{
    public function testSerializeWithUninitializedValues(): void
    {
        $sut = new ParentClassSerializable();

        static::assertEquals(
            '{}',
            Json::serialize($sut),
        );
    }

    public function testSerialize(): void
    {
        $sut = new ParentClassSerializable();
        $sut->name = 'foo';
        $sut->phoneNumbers = ['1234', '5678'];

        static::assertEquals(
            '{"userName":"foo","phoneNumbers":["1234","5678"]}',
            Json::serialize($sut),
        );
    }

    public function testSerializeWithBadPropertyNames(): void
    {
        $sut = new BadPropertyNamesSerializable();
        $sut->name = 'foo';

        static::assertEquals(
            '{"user Name":"foo"}',
            Json::serialize($sut),
        );
    }
}
