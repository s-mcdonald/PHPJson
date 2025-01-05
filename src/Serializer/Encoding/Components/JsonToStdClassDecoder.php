<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Encoding\Components;

use Exception;
use SamMcDonald\Json\Serializer\Encoding\Contracts;
use SamMcDonald\Json\Serializer\Encoding\Contracts\DecodingResultInterface;
use SamMcDonald\Json\Serializer\Encoding\JsonDecodingResult;

readonly class JsonToStdClassDecoder implements Contracts\DecoderInterface
{
    public function __construct(
        private int $depth = 512,
    ) {
    }

    public function decode(string $jsonValue, string|null $fqClassName = null): DecodingResultInterface
    {
        try {
            $decodedData = (object) json_decode($jsonValue, false, $this->depth, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return new JsonDecodingResult(
                '',
                $e->getMessage(),
                false,
            );
        }

        return new JsonDecodingResult(
            $decodedData,
            $fqClassName ?? '',
            true,
        );
    }
}