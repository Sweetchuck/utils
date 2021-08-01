<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Sweetchuck\Utils\EnabledInterface;

class ArrayFilterEnabled extends ArrayFilterBase
{
    protected string $key = 'enabled';

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return $this
     */
    public function setKey(string $value)
    {
        $this->key = $value;

        return $this;
    }

    protected bool $defaultValue = true;

    public function getDefaultValue(): bool
    {
        return $this->defaultValue;
    }

    /**
     * @return $this
     */
    public function setDefaultValue(bool $value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('key', $options)) {
            $this->setKey($options['key']);
        }

        if (array_key_exists('defaultValue', $options)) {
            $this->setDefaultValue($options['defaultValue']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkDoIt($item, ?string $outerKey = null)
    {
        $this->result = null;

        $defaultValue = $this->getDefaultValue();
        $key = $this->getKey();

        if ($item instanceof EnabledInterface) {
            $this->result = $item->isEnabled();
        }

        if ($this->result === null && (is_array($item) || $item instanceof \ArrayAccess)) {
            $this->result = array_key_exists($key, $item) ? $item[$key] : $defaultValue;
        }

        if ($this->result === null && is_bool($item)) {
            $this->result = $item;
        }

        if ($this->result === null && is_object($item) && property_exists($item, $key)) {
            $this->result = $item->$key;
        }

        if ($this->result === null) {
            $this->result = $defaultValue;
        }

        return $this;
    }
}
