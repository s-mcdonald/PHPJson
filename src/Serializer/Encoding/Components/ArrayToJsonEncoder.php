<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Encoding\Components;

use Exception;
use SamMcDonald\Json\JsonFormat;
use SamMcDonald\Json\Serializer\Encoding\Components\Flags\EncodeFlags;
use SamMcDonald\Json\Serializer\Encoding\Components\Flags\EncodeOptions;
use SamMcDonald\Json\Serializer\Encoding\Contracts\ArrayEncoderInterface;
use SamMcDonald\Json\Serializer\Encoding\Contracts\EncodingResultInterface;
use SamMcDonald\Json\Serializer\Encoding\JsonEncodingResult;
use SamMcDonald\Json\Serializer\Encoding\Validator\ValidationMessage;

class ArrayToJsonEncoder implements ArrayEncoderInterface
{
    public function __construct(
        private EncodeOptions|null $options = null,
    ) {
        if (null === $this->options) {
            $this->options = new EncodeOptions(EncodeFlags::create(), 512);
        }
    }

    public function encode(array $value, JsonFormat $format = JsonFormat::Pretty): EncodingResultInterface
    {
        $flags = $this->options->getFlagsValue();

        if (JsonFormat::Pretty === $format) {
            $flags |= JSON_PRETTY_PRINT;
        }

        try {
            $encoded = json_encode($value, JSON_THROW_ON_ERROR | $flags, $this->options->getDepth());
        } catch (Exception $e) {
            return new JsonEncodingResult($e->getMessage());
        }

        if (false === $encoded) {
            return new JsonEncodingResult(ValidationMessage::UNKNOWN_EXCEPTION);
        }

        return new JsonEncodingResult($encoded, isValid: true);
    }
}
