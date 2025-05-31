<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Exceptions;

use RuntimeException;

final class JsonBuilderException extends RuntimeException
{
    public static function createWithMessage(string $message): self
    {
        return new self($message);
    }
}
