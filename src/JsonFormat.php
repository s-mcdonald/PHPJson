<?php

declare(strict_types=1);

namespace SamMcDonald\Json;

enum JsonFormat: string
{
    case Compressed = 'compressed';

    case Pretty = 'pretty';
}
