<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsFilterGroupBaseOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 * @implements \Sweetchuck\Utils\Filter\FilterGroupInterface<TItem>
 *
 * @todo Add @template for callable.
 */
abstract class FilterGroupBase extends FilterBase implements FilterGroupInterface
{

    protected string $type = 'AND';

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @var callable[]
     */
    protected array $filters = [];

    // region \Sweetchuck\Utils\Filter\FilterGroupInterface
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function getFilter(int|string $name): ?callable
    {
        return $this->filters[$name] ?? null;
    }

    public function setFilter(int|string $name, ?callable $filter): static
    {
        if (!$filter) {
            $this->removeFilter($name);

            return $this;
        }

        $this->filters[$name] = $filter;

        return $this;
    }

    public function addFilter(int|string $name, callable $filter): static
    {
        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilters(array $filters): static
    {
        $this->filters = array_replace($this->filters, $filters);

        return $this;
    }

    public function removeFilter(int|string $name): static
    {
        unset($this->filters[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): static
    {
        if ($this->count() === 0) {
            throw new \LogicException('at least one filter is required');
        }

        return $this;
    }
    // endregion

    // region \Countable
    public function count(): int
    {
        return count($this->getFilters());
    }
    // endregion

    /**
     * @phpstan-param SweetchuckUtilsFilterGroupBaseOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('filters', $options)) {
            $this->setFilters($options['filters']);
        }

        return $this;
    }

    protected function setResult(mixed $item, int|string|null $outerKey = null): static
    {
        $this->validate();

        $filters = $this->getFilters();
        $this->result = $this->getType() === 'AND'
            ? $this->getResultAnd($filters, $item, $outerKey)
            : $this->getResultOr($filters, $item, $outerKey);

        return $this;
    }

    /**
     * @phpstan-param array<callable> $filters
     */
    protected function getResultAnd(
        array $filters,
        mixed $item,
        null|int|string $outerKey = null,
    ): bool {
        foreach ($filters as $filter) {
            if (!($filter($item, $outerKey))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @phpstan-param array<callable> $filters
     */
    protected function getResultOr(
        array $filters,
        mixed $item,
        null|int|string $outerKey = null,
    ): bool {
        foreach ($filters as $filter) {
            if ($filter($item, $outerKey)) {
                return true;
            }
        }

        return false;
    }
}
