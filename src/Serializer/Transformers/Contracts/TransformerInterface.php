<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Transformers\Contracts;

interface TransformerInterface
{
    public function transform(mixed $data): string;
}
