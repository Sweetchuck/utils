<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Sweetchuck\Utils\EnabledInterface;

/**
 * Checks if something is enabled.
 *
 * Supported TItem types:
 * - bool
 * - array{CUSTOM_KEY: bool}
 * - \ArrayAccess{CUSTOM_KEY: bool}
 * - \Sweetchuck\Utils\EnabledInterface
 * - object with public ::$CUSTOM_KEY property.
 *
 * @phpstan-import-type SweetchuckUtilsEnabledFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 */
class EnabledFilter extends FilterBase
{
    /**
     * @phpstan-var array<string, bool>
     */
    protected array $stringToBool = [];

    /**
     * @phpstan-return array<string, bool>
     */
    public function getStringToBool(): array
    {
        return $this->stringToBool;
    }

    /**
     * @phpstan-param array<string, bool> $mapping
     */
    public function setStringToBool(array $mapping): static
    {
        $this->stringToBool = $mapping;

        return $this;
    }

    protected int|string $key = 'enabled';

    public function getKey(): int|string
    {
        return $this->key;
    }

    public function setKey(int|string $value): static
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
     * Used when the $key is not exists.
     */
    public function setDefaultValue(bool $value): static
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsEnabledFilterOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('stringToBool', $options)) {
            $this->setStringToBool($options['stringToBool']);
        }

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
    protected function setResult(mixed $item, null|int|string $outerKey = null): static
    {
        $this->result = null;

        $defaultValue = $this->getDefaultValue();
        $key = $this->getKey();

        if ($item instanceof EnabledInterface) {
            $this->result = $item->isEnabled();

            return $this;
        }

        if (is_bool($item)) {
            $this->result = $item;

            return $this;
        }

        if (is_array($item) || $item instanceof \ArrayAccess) {
            $this->result = array_key_exists($key, $item) ?
                $this->convertToBool($item[$key])
                : $defaultValue;

            return $this;
        }

        if (is_object($item)) {
            $this->result = property_exists($item, (string) $key) ?
                $this->convertToBool($item->$key)
                : $defaultValue;

            return $this;
        }

        if (is_scalar($item)) {
            $this->result = $this->convertToBool($item);

            return $this;
        }

        // Maybe it is resource.
        $this->result = $defaultValue;

        return $this;
    }

    protected function convertToBool(mixed $value): bool
    {
        return is_string($value) && array_key_exists($value, $this->stringToBool) ?
            $this->stringToBool[$value]
            : (bool) $value;
    }
}
