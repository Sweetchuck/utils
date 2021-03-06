<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

use Sweetchuck\Utils\ComparerInterface;

abstract class BaseComparer implements ComparerInterface
{

    protected bool $ascending = true;

    protected int $result = 0;

    public function getAscending(): bool
    {
        return $this->ascending;
    }

    /**
     * {@inheritdoc}
     */
    public function setAscending(bool $ascending)
    {
        $this->ascending = $ascending;

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
        if ($this->result === 0 || $this->getAscending()) {
            return $this->result;
        }

        return $this->result > 0 ? -1 : 1;
    }
}
