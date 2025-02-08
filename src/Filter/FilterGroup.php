<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsFilterGroupOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterGroupBase<TItem>
 */
class FilterGroup extends FilterGroupBase
{

    public function setType(string $type): static
    {
        assert(in_array($type, ['AND', 'OR']));

        $this->type = $type;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsFilterGroupOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('type', $options)) {
            $this->setType($options['type']);
        }

        return $this;
    }
}
