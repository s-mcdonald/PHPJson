<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Fixtures\Entities;

use SamMcDonald\Json\Serializer\Attributes\JsonProperty;

class GoodChildObjectSerializable
{
    public function __construct(
        #[JsonProperty('childProp1')]
        private string $childProperty1,
    ){
    }

    #[JsonProperty('childProp2')]
    public GoodChildObjectSerializable|null $childProperty2 = null;

    public string $childProperty3;
}
