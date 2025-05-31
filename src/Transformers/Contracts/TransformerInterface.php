<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Transformers\Contracts;

interface TransformerInterface
{
    public function transform(mixed $data): string;
}
