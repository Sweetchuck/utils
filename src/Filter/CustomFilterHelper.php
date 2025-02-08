<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * Re-invent the wheel.
 *
 * @see \PHPUnit\Framework\Assert
 *
 */
class CustomFilterHelper
{

    /**
     * @todo UpperCamel
     * @todo Enum.
     */
    public static function normalizeOperator(string $operator): string
    {
        return match ($operator) {
            'lt',
            'less',
            'lessThan',
            'less_than' => '<',

            'le',
            'lessOrEqual',
            'lessThanOrEqual',
            'less_or_equal',
            'less_than_or_equal' => '<=',

            '=',
            'eq',
            'equal',
            'equalLazy',
            'equal_lazy' => '==',

            'equalStrict',
            'equal_strict' => '===',

            'ge',
            'gtOrEq',
            'gt_or_eq',
            'greaterThanOrEqual',
            'greater_than_or_equal',
            'greaterOrEqual',
            'greater_or_equal' => '>=',

            'gt',
            'greater',
            'greaterThan',
            'greater_than' => '>',

            default => $operator,
        };
    }

    public static function createFromOperator(string $operator): callable
    {
        switch (static::normalizeOperator($operator)) {
            case 'empty':
                return static::empty(...);

            case 'isset':
                return static::isset(...);

            case 'instanceOf':
                return static::instanceOf(...);

            case '<':
                return static::lessThan(...);

            case '<=':
                return static::lessThanOrEqual(...);

            case '==':
                return static::equalLazy(...);

            case '===':
                return static::equalStrict(...);

            case '>=':
                return static::greaterThanOrEqual(...);

            case '>':
                return static::greaterThan(...);

            case 'between':
                return static::between(...);
        }

        throw new \InvalidArgumentException("Operator $operator not found");
    }

    public static function empty(mixed $value): bool
    {
        return empty($value);
    }

    public static function isset(mixed $value): bool
    {
        return isset($value);
    }

    public static function instanceOf(object $value1, string|object $value2): bool
    {
        return $value1 instanceof $value2;
    }

    public static function lessThan(mixed $value1, mixed $value2): bool
    {
        return $value1 < $value2;
    }

    public static function lessThanOrEqual(mixed $value1, mixed $value2): bool
    {
        return $value1 <= $value2;
    }

    public static function equalLazy(mixed $value1, mixed $value2): bool
    {
        return $value1 == $value2;
    }

    public static function equalStrict(mixed $value1, mixed $value2): bool
    {
        return $value1 === $value2;
    }

    public static function greaterThanOrEqual(mixed $value1, mixed $value2): bool
    {
        return $value1 >= $value2;
    }

    public static function greaterThan(mixed $value1, mixed $value2): bool
    {
        return $value1 > $value2;
    }

    public static function between(
        mixed $value,
        mixed $min,
        mixed $max,
        string $opMin = '>=',
        string $opMax = '<=',
    ): bool {
        $opMin = static::normalizeOperator($opMin);
        assert($opMin === '>=' || $opMin === '>');
        $op = static::createFromOperator($opMin);
        if (!$op($value, $min)) {
            return false;
        }

        $opMax = static::normalizeOperator($opMax);
        assert($opMax === '<=' || $opMax === '<');
        $op = static::createFromOperator($opMax);

        return $op($value, $max);
    }

    // @todo two arrays - any of
    // @todo two arrays - all of
    // @todo two arrays - none of
    // @todo contains    *=
    // @todo regexp      ~=
}
