<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsArrayAllowedValueFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 */
class ArrayAllowedValueFilter extends FilterBase
{
    protected int|string $key = 'name';

    public function getKey(): int|string
    {
        return $this->key;
    }

    public function setKey(int|string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @var mixed[]
     */
    protected array $allowedValues = [];

    /**
     * @return mixed[]
     */
    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }

    /**
     * @param mixed[] $allowedValues
     */
    public function setAllowedValues(array $allowedValues): static
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsArrayAllowedValueFilterOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('key', $options)) {
            $this->setKey($options['key']);
        }

        if (array_key_exists('allowedValues', $options)) {
            $this->setAllowedValues($options['allowedValues']);
        }

        return $this;
    }

    protected function setResult(mixed $item, null|int|string $outerKey = null): static
    {
        $this->result = in_array(
            $item[$this->getKey()] ?? null,
            $this->getAllowedValues(),
        );

        return $this;
    }
}
