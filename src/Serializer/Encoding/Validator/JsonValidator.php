<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Encoding\Validator;

use SamMcDonald\Json\Serializer\Encoding\Validator\Contracts\JsonValidatorInterface;
use SamMcDonald\Json\Serializer\Encoding\Validator\Exceptions\JsonDecodeException;

class JsonValidator implements JsonValidatorInterface
{
    public function isValid(string $json): bool
    {
        if (empty($json)) {
            return false;
        }

        \json_decode($json);

        return false === $this->getLastJsonErrorCode();
    }

    public function validate(string $json): void
    {
        if (false === $this->isValid($json)) {
            throw new JsonDecodeException(json_last_error_msg());
        }
    }

    public function getLastErrorMessage(): string|null
    {
        $lastMessage = $this->getLastErrorMessage();

        if (false === $lastMessage) {
            return null;
        }

        return $lastMessage;
    }

    private function getLastJsonErrorCode(): false|string
    {
        return match (json_last_error()) {
            JSON_ERROR_NONE => false,
            JSON_ERROR_DEPTH => 'The maximum stack depth was exceeded.',
            JSON_ERROR_INVALID_PROPERTY_NAME => 'The property name is invalid.',
            JSON_ERROR_STATE_MISMATCH => 'Malformed Json.',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character was found within the Json string.',
            JSON_ERROR_SYNTAX => 'Syntax error.',
            JSON_ERROR_UTF8 => 'Invalid UTF-8 characters.',
            default => $this->getDefaultJsonErrorMessage(),
        };
    }

    private function getDefaultJsonErrorMessage(): string
    {
        return \sprintf(
            'The following decoding error was encountered: `%s`',
            json_last_error_msg(),
        );
    }
}
