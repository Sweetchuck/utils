<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

class ArrayFilterGroupOr extends ArrayFilterGroup
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
