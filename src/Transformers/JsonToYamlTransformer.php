<?php

declare(strict_types=1);

namespace SamMcDonald\Json\Transformers;

use RuntimeException;
use SamMcDonald\Json\Serializer\Encoding\Contracts\DecoderInterface;
use SamMcDonald\Json\Transformers\Configuration\YamlConfiguration;
use SamMcDonald\Json\Transformers\Contracts\TransformerInterface;

/**
 * @experimental
 */
final readonly class JsonToYamlTransformer implements TransformerInterface
{
    public function __construct(
        private DecoderInterface $jsonDecoder,
        private YamlConfiguration $configuration,
    ) {
    }

    public function transform(mixed $data): string
    {
        assert(is_string($data));

        $decodedPacket = $this->jsonDecoder->decode($data);

        if (false === $decodedPacket->isValid()) {
            throw new RuntimeException('Failed to decode json to array.');
        }

        return $this->arrayToYaml($decodedPacket->getBody());
    }

    private function arrayToYaml(mixed $data, int $level = 0): string
    {
        if (!is_array($data)) {
            return $this->formatScalarValue($data);
        }

        $indent = $this->configuration->getIndentWidth();

        $isSequential = array_keys($data) === range(0, count($data) - 1);
        $output = '';
        $baseIndent = str_repeat(' ', $level * $indent);

        foreach ($data as $key => $value) {
            $output .= $baseIndent;

            if (!$isSequential) {
                $output .= $this->formatKey((string) $key) . ': ';
            } else {
                $output .= '- ';
            }

            if (is_array($value)) {
                if (empty($value)) {
                    $output .= '[]' . PHP_EOL;
                } else {
                    $output .= PHP_EOL;
                    $newLevel = $isSequential ? $level + 1 : $level + 1;
                    $output .= $this->arrayToYaml($value, $newLevel, $indent);
                }
            } else {
                $output .= $this->formatScalarValue($value) . PHP_EOL;
            }
        }

        return $output;
    }

    private function formatScalarValue(mixed $value): string
    {
        if (null === $value) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            if (
                '' === $value
                || is_numeric($value)
                || preg_match('/[:#\[\]{}&*!|>\'"%@`]/', $value)
                || in_array(mb_strtolower($value), ['true', 'false', 'null', 'yes', 'no', 'on', 'off'])
            ) {
                return '"' . str_replace('"', '\\"', $value) . '"';
            }

            return $value;
        }

        return (string) $value;
    }

    private function formatKey(string $key): string
    {
        if (
            '' === $key || preg_match('/[:#\[\]{}&*!|>\'"%@`\s]/', $key)
        ) {
            return '"' . str_replace('"', '\\"', $key) . '"';
        }

        return $key;
    }
}
