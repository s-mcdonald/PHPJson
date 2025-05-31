<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Loaders;

use InvalidArgumentException;
use RuntimeException;
use SamMcDonald\Json\Loaders\Contracts\LoaderInterface;

class LocalFileLoader implements LoaderInterface
{
    final public function __construct()
    {
    }

    public function load(mixed $data): string
    {
        if (!is_string($data)) {
            throw new InvalidArgumentException('The data must be a string.');
        }

        if (!file_exists($data)) {
            throw new RuntimeException(
                sprintf(
                    'The file %s does not exist or can not be found.',
                    $data,
                ),
            );
        }

        return file_get_contents($data);
    }
}
