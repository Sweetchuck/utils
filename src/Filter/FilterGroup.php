<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Sweetchuck\Utils\Phpstan;

/**
 * @phpstan-import-type SweetchuckUtilsFilterGroupOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 *
 * @todo Add @template for callable.
 */
abstract class FilterGroup extends FilterBase
{

    /**
     * @var callable[]
     */
    protected array $filters = [];

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param callable[] $filters
     */
    public function setFilters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function addFilter(int|string $name, callable $filter): static
    {
        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * @param callable[] $filters
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
     * @phpstan-param SweetchuckUtilsFilterGroupOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('filters', $options)) {
            $this->setFilters($options['filters']);
        }

        return $this;
    }
}
