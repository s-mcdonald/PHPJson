<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Formatter;

/**
 * @deprecated
 */
final class JsonFormatter
{
    public function ugly(string $json): string
    {
        return json_encode(json_decode($json, false), JSON_THROW_ON_ERROR);
    }
}
