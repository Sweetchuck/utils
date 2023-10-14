<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @implements \Sweetchuck\Utils\Filter\FilterInterface<TItem>
 */
abstract class FilterBase implements FilterInterface
{

    protected bool $inverse = false;

    public function getInverse(): bool
    {
        return $this->inverse;
    }

    public function setInverse(bool $value): static
    {
        $this->inverse = $value;

        return $this;
    }

    protected ?bool $result = null;

    /**
     * @phpstan-param SweetchuckUtilsFilterOptions $options
     */
    public function setOptions(array $options): static
    {
        if (array_key_exists('inverse', $options)) {
            $this->setInverse($options['inverse']);
        }

        return $this;
    }

    /**
     * @phpstan-param TItem $item
     */
    public function __invoke(mixed $item, null|int|string $outerKey = null): bool
    {
        return $this->isAllowed($item, $outerKey);
    }

    /**
     * @phpstan-param TItem $item
     */
    public function isAllowed(mixed $item, null|int|string $outerKey = null): bool
    {
        return $this
            ->setResult($item, $outerKey)
            ->getFinalResult();
    }

    /**
     * @phpstan-param TItem $item
     */
    abstract protected function setResult(mixed $item, null|int|string $outerKey = null): static;

    protected function getFinalResult(): bool
    {
        return $this->getInverse() ? !$this->result : (bool) $this->result;
    }
}
