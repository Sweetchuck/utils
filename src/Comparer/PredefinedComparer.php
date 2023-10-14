<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * Only for scalar values.
 *
 * @phpstan-import-type SweetchuckUtilsPredefinedComparerOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Comparer\ComparerBase<TItem>
 */
class PredefinedComparer extends ComparerBase
{

    /**
     * @phpstan-var array<int|float>
     */
    protected array $weights = [];

    /**
     * @phpstan-return array<int|float>
     */
    public function getWeights(): array
    {
        return $this->weights;
    }

    /**
     * @phpstan-param array<int|float> $weights
     */
    public function setWeights(array $weights): static
    {
        $this->weights = $weights;

        return $this;
    }

    protected int|float $defaultWeight = 0;

    public function getDefaultWeight(): int|float
    {
        return $this->defaultWeight;
    }

    public function setDefaultWeight(int|float $defaultWeight): static
    {
        $this->defaultWeight = $defaultWeight;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsPredefinedComparerOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('weights', $options)) {
            $this->setWeights($options['weights']);
        }

        if (array_key_exists('defaultWeight', $options)) {
            $this->setDefaultWeight($options['defaultWeight']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult($a, $b): static
    {
        $weights = $this->getWeights();
        $defaultWeight = $this->getDefaultWeight();

        $this->result = ($weights[$a] ?? $defaultWeight) <=> ($weights[$b] ?? $defaultWeight);

        return $this;
    }
}
