<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * Only for scalar values.
 */
class PredefinedComparer extends BaseComparer
{

    public function __construct(array $weights = [], int $defaultWeight = 0)
    {
        $this->setWeights($weights);
        $this->setDefaultWeight($defaultWeight);
    }

    /**
     * @var int[]
     */
    protected array $weights = [];

    /**
     * @return int[]
     */
    public function getWeights(): array
    {
        return $this->weights;
    }

    /**
     * @param int[] $weights
     *
     * @return $this
     */
    public function setWeights(array $weights)
    {
        $this->weights = $weights;

        return $this;
    }

    protected int $defaultWeight = 0;

    public function getDefaultWeight(): int
    {
        return $this->defaultWeight;
    }

    /**
     * @return $this
     */
    public function setDefaultWeight(int $defaultWeight)
    {
        $this->defaultWeight = $defaultWeight;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult($a, $b)
    {
        $weights = $this->getWeights();
        $defaultWeight = $this->getDefaultWeight();

        $this->result = ($weights[$a] ?? $defaultWeight) <=> ($weights[$b] ?? $defaultWeight);

        return $this;
    }
}
