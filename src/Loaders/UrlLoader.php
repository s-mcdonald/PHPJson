<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Loaders;

use InvalidArgumentException;
use RuntimeException;
use SamMcDonald\Json\Loaders\Contracts\LoaderInterface;

class UrlLoader implements LoaderInterface
{
    final public function __construct()
    {
    }

    public function load(mixed $data): string
    {
        if (!filter_var($data, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                sprintf('The provided URL "%s" is not valid.', $data),
            );
        }

        if (!ini_get('allow_url_fopen')) {
            throw new RuntimeException(
                'The "allow_url_fopen" setting is disabled. Enable it to load the URL.',
            );
        }

        $content = @file_get_contents($data);
        if (false === $content) {
            throw new RuntimeException(
                sprintf(
                    'Failed to load content from URL "%s". The file may not exist or is not accessible.',
                    $data,
                ),
            );
        }

        return $content;
    }
}
