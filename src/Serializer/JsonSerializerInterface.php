<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer;

use SamMcDonald\Json\JsonFormat;

interface JsonSerializerInterface
{
    public function serialize(mixed $object, JsonFormat $format = JsonFormat::Compressed): string;

    public function deserialize(string $json, string $classFqn): mixed;
}
