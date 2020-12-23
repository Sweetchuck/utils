<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

class StringFormat
{

    /**
     * @code
     * $format = 'a %{foo.s} b';
     * $values = ['foo' => 'bar'];
     * echo ::vsprintf($format, $values); // a bar b
     * @endcode
     */
    public static function vsprintf(string $format, array $values = []): string
    {
        $args = static::anonymizePrintPlaceholders($format, $values);

        return vsprintf($args['format'], $args['values']);
    }

    public static function anonymizePrintPlaceholders(
        string $format,
        array $values
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
}
