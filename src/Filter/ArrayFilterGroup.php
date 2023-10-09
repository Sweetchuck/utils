<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

abstract class ArrayFilterGroup extends ArrayFilterBase
{

    /**
     * @phpstan-var array<string, callable>
     */
    protected array $filters = [];

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return $this
     */
    public function addFilter(string $name, callable $filter)
    {
        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * @return $this
     */
    public function addFilters(array $filters)
    {
        $this->filters = array_replace($this->filters, $filters);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeFilter(string $name)
    {
        unset($this->filters[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('filters', $options)) {
            $this->setFilters($options['filters']);
        }

        return $this;
    }
}
