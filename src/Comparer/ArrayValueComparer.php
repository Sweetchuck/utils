<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * @phpstan-import-type SweetchuckUtilsComparer from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type SweetchuckUtilsArrayValueComparerOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Comparer\ComparerBase<TItem>
 */
class ArrayValueComparer extends ComparerBase
{

    /**
     * @phpstan-var iterable<string, mixed>
     */
    protected iterable $keys = [];

    /**
     * @phpstan-return iterable<string, mixed>
     */
    public function getKeys(): iterable
    {
        return $this->keys;
    }

    /**
     * @phpstan-param iterable<string, mixed> $value
     */
    public function setKeys(iterable $value): static
    {
        $this->keys = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-param SweetchuckUtilsArrayValueComparerOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('keys', $options)) {
            $this->setKeys($options['keys']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult($a, $b): static
    {
        foreach ($this->getKeys() as $key => $info) {
            $info = $this->normalizeInfo($key, $info);
            $aValue = $this->fetchValue($info, $a);
            $bValue = $this->fetchValue($info, $b);

            $comparer = $info['comparer'] ?? $this->getDefaultComparer($aValue, $bValue);
            $this->result = $comparer ? $comparer($aValue, $bValue) : $aValue <=> $bValue;
            $this->result *= $info['direction']->value;

            if ($this->result !== ComparisonResult::Equal->value) {
                break;
            }
        }

        return $this;
    }

    /**
     * @phpstan-param mixed $key
     * @phpstan-param string|array<string, mixed> $info
     *
     * @phpstan-return array<string, mixed>
     */
    protected function normalizeInfo(mixed $key, mixed $info): array
    {
        if (!is_array($info)) {
            return [
                'key' => $key,
                'comparer' => null,
                'default' => $info,
                'direction' => OrderDirection::ASC,
            ];
        }

        $info['key'] = $key;

        $info += [
            'comparer' => null,
            'default' => null,
            'direction' => OrderDirection::ASC,
        ];

        if (is_string($info['direction'])) {
            $info['direction'] = OrderDirection::fromName($info['direction']);
        } elseif (is_int($info['direction'])) {
            $info['direction'] = OrderDirection::from($info['direction']);
        }

        return $info;
    }

    /**
     * @phpstan-param array<string, mixed> $info
     * @phpstan-param TItem $item
     */
    protected function fetchValue(array $info, mixed $item): mixed
    {
        $key = $info['key'];

        return $item instanceof \ArrayAccess ?
            ($item->offsetExists($key) ? $item[$key] : $info['default'])
            : (array_key_exists($key, $item) ? $item[$key] : $info['default']);
    }

    /**
     * @phpstan-return null|SweetchuckUtilsComparer
     */
    protected function getDefaultComparer(mixed $aValue, mixed $bValue): ?callable
    {
        $type = $aValue !== null ? gettype($aValue) : gettype($bValue);

        // @phpstan-ignore-next-line return.type
        return $type === 'string' ? '\strnatcmp' : null;
    }
}
