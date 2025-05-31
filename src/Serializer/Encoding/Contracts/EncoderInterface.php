<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Encoding\Contracts;

use SamMcDonald\Json\JsonFormat;
use stdClass;

interface EncoderInterface
{
    public function encode(stdClass $value, JsonFormat $format = JsonFormat::Pretty): EncodingResultInterface;
}
