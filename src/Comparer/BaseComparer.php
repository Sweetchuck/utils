<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

use Sweetchuck\Utils\ComparerInterface;

abstract class BaseComparer implements ComparerInterface
{

    protected int $direction = 1;

    protected int $result = 0;

    public function getDirection(): int
    {
        return $this->direction;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirection(int $direction)
    {
        assert($direction !== 0);

        $this->direction = $direction > 0 ? self::DIR_ASCENDING : self::DIR_DESCENDING;

        return $this;
    }

    public function __invoke($a, $b): int
    {
        return $this->compare($a, $b);
    }

    public function compare($a, $b): int
    {
        return $this
            ->initResult()
            ->setResult($a, $b)
            ->getResult();
    }

    /**
     * @return $this
     */
    protected function initResult()
    {
        $this->result = 0;

        return $this;
    }

    /**
     * @return $this
     */
    abstract protected function setResult($a, $b);

    protected function getResult(): int
    {
        return $this->result * $this->getDirection();
    }

    /**
     * @param bool|int $input
     */
    protected function normalizeDirection($input): int
    {
        if (is_bool($input)) {
            return $input ? self::DIR_ASCENDING : self::DIR_DESCENDING;
        }

        return intval($input) > 0 ? self::DIR_ASCENDING : self::DIR_DESCENDING;
    }
}
