<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

/**
 * @phpstan-import-type UrlParts from \Sweetchuck\Utils\Phpstan
 */
class StringUtils
{

    /**
     * Similar to \vsprintf just with named placeholders and arguments.
     *
     * @code
     * $format = 'a %{foo.s} b';
     * $values = ['foo' => 'bar'];
     * echo ::vsprintf($format, $values); // a bar b
     * @endcode
     *
     * @phpstan-param array<mixed> $values
     */
    public function vsprintf(string $format, array $values = []): string
    {
        $args = static::anonymizePrintPlaceholders($format, $values);

        return vsprintf($args['format'], $args['values']);
    }

    /**
     * @phpstan-param array<string, mixed> $values
     *
     * @phpstan-return array<string, mixed>
     */
    public function anonymizePrintPlaceholders(
        string $format,
        array $values,
    ): array {
        $return = [
            'format' => $format,
            'values' => [],
        ];

        if (!$values) {
            return $return;
        }

        $keys = [];
        foreach (array_keys($values) as $key) {
            $keys[] = preg_quote($key);
        }

        $return['format'] = preg_replace_callback(
            '/(?P<percentSymbols>%+){(?P<name>' . implode('|', $keys) . ')\.(?P<pattern>.+?)}/',
            function ($matches) use ($values, &$return) {
                $numOfPercentSymbols = mb_strlen($matches['percentSymbols']);
                if ($numOfPercentSymbols % 2 === 0) {
                    return $matches[0];
                }

                $return['values'][] = $values[$matches['name']];

                return $matches['percentSymbols'] . $matches['pattern'];
            },
            $format,
        );

        return $return;
    }

    /**
     * @phpstan-param UrlParts $parts
     * @phpstan-param "placeholder"|"hidden"|"raw" $passwordFormat
     *
     * @see \parse_url()
     * @see \http_build_url()
     */
    public function buildUri(array $parts, string $passwordFormat = 'raw'): string
    {
        $uri = '';
        if (isset($parts['scheme']) && $parts['scheme'] !== '') {
            $uri .= $parts['scheme'] . '://';
        }

        if (isset($parts['user']) && $parts['user'] !== '') {
            $uri .= urlencode($parts['user']);
            if (isset($parts['pass']) && $parts['pass'] !== '') {
                switch ($passwordFormat) {
                    case 'placeholder':
                        $uri .= ':****';
                        break;
                    case 'hidden':
                        break;
                    default:
                        $uri .= ':' . urlencode($parts['pass']);
                        break;
                }
            }

            $uri .= '@';
        }

        if (isset($parts['host']) && $parts['host'] !== '') {
            $uri .= $parts['host'];
            if (!empty($parts['port'])) {
                $uri .= ':' . $parts['port'];
            }
        }

        if (isset($parts['path']) && $parts['path'] !== '') {
            if (substr($parts['path'], 0, 1) !== '/') {
                $uri .= '/';
            }

            $uri .= $parts['path'];
        }

        if (!empty($parts['query'])) {
            $uri .= '?' . (is_string($parts['query']) ? $parts['query'] : http_build_query($parts['query']));
        }

        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $uri .= '#' . $parts['fragment'];
        }

        return $uri;
    }
}
