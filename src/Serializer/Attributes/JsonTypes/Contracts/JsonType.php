<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Serializer\Attributes\JsonTypes\Contracts;

abstract class JsonType
{
    protected const SYSTEM_TYPE_MAP = [
        'int' => 'integer',
        'float' => 'double',
        'bool' => 'boolean',
        'string' => 'string',
        'array' => 'array',
        'object' => 'object',
        'null' => 'NULL',
        'resource' => 'resource',
        'mixed' => 'mixed',
        'callable' => 'callable',
        'iterable' => 'iterable',
        'void' => 'void',
        'never' => 'never',
        'self' => 'self',
        'static' => 'static',
    ];

    final public function __construct()
    {
    }

    /**
     * The system type.
     */
    abstract public function getPhpType(): string;

    /**
     * The type hint.
     */
    final public function getTypeHint(): string
    {
        return $this->resolve($this->getPhpType());
    }

    /**
     * Returns an array of all compatible types we can cast from.
     */
    abstract public function getCompatibleCastTypes(): array;

    final public function canCast($value): bool
    {
        if (in_array(\gettype($value), $this->getCompatibleCastTypes(), true)) {
            return true;
        }

        return false;
    }

    final public function casts($value): mixed
    {
        if (false === $this->canCast($value)) {
            return $value;
        }

        return $this->cast($value);
    }

    protected function resolve(string $type): string
    {
        return self::resolveTypeHint($type);
    }

    protected static function resolveTypeHint(string $type): string
    {
        return self::SYSTEM_TYPE_MAP[$type] ?? $type;
    }

    abstract protected function cast($value): mixed;
}
