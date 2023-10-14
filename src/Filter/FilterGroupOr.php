<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterGroup<TItem>
 */
class FilterGroupOr extends FilterGroup
{

    /**
     * {@inheritdoc}
     */
    protected function setResult($item, null|int|string $outerKey = null): static
    {
        $filters = $this->getFilters();
        if (!$filters) {
            throw new \LogicException('at least one filter is required');
        }

        $this->result = false;
        foreach ($filters as $filter) {
            $result = $filter($item, $outerKey);
            if ($result) {
                $this->result = true;

                break;
            }
        }

        return $this;
    }
}
