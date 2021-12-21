<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

interface ComparerInterface
{
    const DIR_ASCENDING = 1;

    const DIR_DESCENDING = -1;

    public function getDirection(): int;

    /**
     * @param int $direction
     *   Allowed values: 1, -1.
     *
     * @return $this
     *
     * @see \Sweetchuck\Utils\ComparerInterface::DIR_ASCENDING
     * @see \Sweetchuck\Utils\ComparerInterface::DIR_DESCENDING
     */
    public function setDirection(int $direction);

    public function __invoke($a, $b): int;

    public function compare($a, $b): int;
}
