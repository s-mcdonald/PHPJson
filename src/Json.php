<?php

declare(strict_types=1);

namespace SamMcDonald\Json;

use SamMcDonald\Json\Builder\JsonBuilder;
use SamMcDonald\Json\Serializer\Contracts\JsonSerializable;
use SamMcDonald\Json\Serializer\Enums\JsonFormat;
use SamMcDonald\Json\Serializer\Formatter\JsonFormatter;
use SamMcDonald\Json\Serializer\JsonSerializer;

final class Json
{
    private function __construct()
    {
    }

    public static function serialize(
        JsonSerializable $object,
        JsonFormat $format = JsonFormat::Compressed,
    ): string {
        return (new JsonSerializer())->serialize($object, $format);
    }

    public static function deserialize(string $json, string $classFqn): mixed
    {
        return (new JsonSerializer())->deserialize($json, $classFqn);
    }

    public static function createJsonBuilder(): JsonBuilder
    {
        return new JsonBuilder();
    }

    public static function prettify(string $json): string
    {
        return (new JsonFormatter())->pretty($json);
    }

    public static function uglify(string $json): string
    {
        return (new JsonFormatter())->ugly($json);
    }
}
