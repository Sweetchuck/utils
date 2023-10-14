<?php

declare(strict_types = 1);


namespace Sweetchuck\Utils\Filter;

class CustomFilterHelper
{

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

    public static function equalLazy(mixed $value1, mixed $value2): bool
    {
        return $value1 == $value2;
    }

    public static function equalStrict(mixed $value1, mixed $value2): bool
    {
        return $value1 === $value2;
    }

    public static function gtOrEq(mixed $value1, mixed $value2): bool
    {
        return $value1 >= $value2;
    }

    public static function ltOrEq(mixed $value1, mixed $value2): bool
    {
        return $value1 <= $value2;
    }

    public static function lessThan(mixed $value1, mixed $value2): bool
    {
        return $value1 < $value2;
    }

    public static function greaterThan(mixed $value1, mixed $value2): bool
    {
        return $value1 > $value2;
    }
}
