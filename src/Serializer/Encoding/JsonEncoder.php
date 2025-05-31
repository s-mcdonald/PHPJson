<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Encoding;

use Exception;
use SamMcDonald\Json\JsonFormat;
use SamMcDonald\Json\Serializer\Encoding\Components\Flags\EncodeFlags;
use SamMcDonald\Json\Serializer\Encoding\Components\Flags\EncodeOptions;
use SamMcDonald\Json\Serializer\Encoding\Contracts\EncoderInterface;
use SamMcDonald\Json\Serializer\Encoding\Contracts\EncodingResultInterface;
use SamMcDonald\Json\Serializer\Encoding\Validator\Contracts\JsonValidatorInterface;
use SamMcDonald\Json\Serializer\Encoding\Validator\ValidationMessage;
use stdClass;

class JsonEncoder implements EncoderInterface
{
    public function __construct(
        private readonly JsonValidatorInterface $validator,
        private EncodeOptions|null $options = null,
    ) {
        if (null === $this->options) {
            $this->options = new EncodeOptions(EncodeFlags::create(), 512);
        }
    }

    public function encode(stdClass $value, JsonFormat $format = JsonFormat::Pretty): EncodingResultInterface
    {
        $flags = $this->options->getFlagsValue();

        if (JsonFormat::Pretty === $format) {
            $flags |= JSON_PRETTY_PRINT;
        }

        try {
            $encoded = json_encode($value, JSON_THROW_ON_ERROR | $flags, $this->options->getDepth());
        } catch (Exception $e) {
            return new JsonEncodingResult($this->validator->getLastErrorMessage());
        }

        if (false === $encoded) {
            return new JsonEncodingResult(ValidationMessage::UNKNOWN_EXCEPTION);
        }

        return new JsonEncodingResult($encoded, isValid: true);
    }
}
