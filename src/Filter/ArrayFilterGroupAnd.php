<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

class ArrayFilterGroupAnd extends ArrayFilterGroup
{

    /**
     * {@inheritdoc}
     */
    protected function checkDoIt($item, ?string $outerKey = null)
    {
        $filters = $this->getFilters();
        if (!$filters) {
            throw new \LogicException('at least one filter is required');
        }

        $this->result = true;
        foreach ($filters as $filter) {
            $result = $filter($item, $outerKey);
            if (!$result) {
                $this->result = false;

                break;
            }
        }

        return $this;
    }
}
