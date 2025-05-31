<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Transformers\Configuration;

use InvalidArgumentException;

readonly class YamlConfiguration
{
    public function __construct(
        private int $indentWidth = 2,
    ) {
        if (!in_array($this->indentWidth, [2, 4])) {
            throw new InvalidArgumentException('Indent width must be 2 or 4');
        }
    }

    public function getIndentWidth(): int
    {
        return $this->indentWidth;
    }
}
