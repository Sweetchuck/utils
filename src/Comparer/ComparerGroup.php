<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * @phpstan-import-type SweetchuckUtilsComparer from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type SweetchuckUtilsComparerChainOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Comparer\ComparerBase<TItem>
 */
class ComparerGroup extends ComparerBase
{
    /**
     * @phpstan-var array<string, SweetchuckUtilsComparer>
     */
    protected array $comparers = [];

    /**
     * @phpstan-return array<string, SweetchuckUtilsComparer>
     */
    public function getComparers(): array
    {
        return $this->comparers;
    }

    /**
     * @phpstan-param array<string, SweetchuckUtilsComparer> $comparers
     */
    public function setComparers(array $comparers): static
    {
        $this->comparers = $comparers;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsComparer $comparer
     */
    public function addComparer(string $name, callable $comparer): static
    {
        $this->comparers[$name] = $comparer;

        return $this;
    }

    /**
     * @phpstan-param array<string, SweetchuckUtilsComparer> $comparers
     */
    public function addComparers(array $comparers): static
    {
        $this->comparers = array_replace($this->comparers, $comparers);

        return $this;
    }

    public function removeComparer(string $name): static
    {
        unset($this->comparers[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-param SweetchuckUtilsComparerChainOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('comparers', $options)) {
            $this->setComparers($options['comparers']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult($a, $b): static
    {
        $comparers = $this->getComparers();
        if (!$comparers) {
            throw new \LogicException('at least one comparer is required');
        }

        foreach ($comparers as $comparer) {
            $result = $comparer($a, $b);
            if ($result) {
                $this->result = $result;

                break;
            }
        }

        return $this;
    }
}
