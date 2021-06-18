<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

class ArrayFilterByPropertyValue extends ArrayFilterBase
{
    protected string $property = 'name';

    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return $this
     */
    public function setProperty(string $property)
    {
        $this->property = $property;

        return $this;
    }

    protected array $allowedValues = [];

    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }

    /**
     * @return $this
     */
    public function setAllowedValues(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('property', $options)) {
            $this->setProperty($options['property']);
        }

        if (array_key_exists('allowedValues', $options)) {
            $this->setAllowedValues($options['allowedValues']);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function checkDoIt($item, ?string $outerKey = null)
    {
        $this->result = array_key_exists(
            $item[$this->getProperty()] ?? '',
            $this->getAllowedValues()
        );

        return $this;
    }
}
