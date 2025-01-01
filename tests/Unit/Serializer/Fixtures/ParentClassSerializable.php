<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Tests\Unit\Serializer\Fixtures;

use SamMcDonald\Json\Serializer\Attributes\JsonProperty;
use SamMcDonald\Json\Serializer\Contracts\JsonSerializable;

class ParentClassSerializable implements JsonSerializable
{
    #[JsonProperty('userName', deserialize: true)]
    public string $name;

    #[JsonProperty]
    public array $phoneNumbers;

    #[JsonProperty('userAddress', deserialize: true)]
    private string $address;

    #[JsonProperty('child')]
    public GoodChildObjectSerializable $child;

    public function __construct(
        private readonly int|null $creditCard = null,
    ) {
    }

    #[JsonProperty('creditCard')]
    public function getCreditCard(): int|null
    {
        return $this->creditCard;
    }
}
