<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

enum OrderDirection: int
{
    case ASC = 1;

    case DESC = -1;

    public static function tryFromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === strtoupper($name)) {
                return $case;
            }
        }

        return null;
    }

    public static function fromName(string $name): self
    {
        $case = self::tryFromName($name);
        if ($case === null) {
            throw new \ValueError(sprintf(
                'Enum %s::%s is not exists',
                self::class,
                $name,
            ));
        }

        return $case;
    }

    public static function fromBool(bool $direction): self
    {
        return self::from($direction ? 1 : -1);
    }
}
