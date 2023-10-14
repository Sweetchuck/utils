<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * @phpstan-import-type ComparisonResultValue from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @implements \Sweetchuck\Utils\Comparer\ComparerInterface<TItem>
 */
abstract class ComparerBase implements ComparerInterface
{

    protected int $result = ComparisonResult::Equal->value;

    protected OrderDirection $direction = OrderDirection::ASC;

    public function getDirection(): OrderDirection
    {
        return $this->direction;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirection(int|string|OrderDirection $direction): static
    {
        if (is_int($direction)) {
            $direction = OrderDirection::from($direction);
        } elseif (is_string($direction)) {
            $direction = OrderDirection::fromName($direction);
        }

        $this->direction = $direction;

        return $this;
    }

    public function setOptions(array $options): static
    {
        if (array_key_exists('direction', $options)) {
            $this->setDirection($options['direction']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($a, $b): int
    {
        return $this->compare($a, $b);
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b): int
    {
        return $this
            ->initResult()
            ->setResult($a, $b)
            ->getResult();
    }

    protected function initResult(): static
    {
        $this->result = ComparisonResult::Equal->value;

        return $this;
    }

    /**
     * @phpstan-param TItem $a
     * @phpstan-param TItem $b
     */
    abstract protected function setResult($a, $b): static;

    /**
     * @phpstan-return ComparisonResultValue
     */
    protected function getResult(): int
    {
        // @phpstan-ignore-next-line
        return $this->result * $this->getDirection()->value;
    }
}
