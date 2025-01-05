<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Encoding;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Json\Serializer\Hydration\Exceptions\HydrationException;
use SamMcDonald\Json\Serializer\Hydrator;
use SamMcDonald\Json\Tests\Fixtures\Entities\SimplePropertiesNoOverrideClass;

class HydratorTest extends TestCase
{
    public function testHydrationFailureUsingBadArrayKeyForPropertyName(): void
    {
        $this->expectException(HydrationException::class);

        $input = ["name" => "foo-name", ["age" => 44] ];

        $sut = new Hydrator();

        $sut->hydrate($input, SimplePropertiesNoOverrideClass::class);
    }

    public function testHydrationBasic(): void
    {
        $expected = new SimplePropertiesNoOverrideClass('foo-name', 44);

        $input = ["name" => "foo-name", "age" => 44 ];

        $sut = new Hydrator();

        $hydrated = $sut->hydrate($input, SimplePropertiesNoOverrideClass::class);
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
}
