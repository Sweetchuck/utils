<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

class ArrayValueComparer extends BaseComparer
{

    public function __construct(array $keys = [])
    {
        $this->setKeys($keys);
    }

    protected iterable $keys = [];

    public function getKeys(): iterable
    {
        return $this->keys;
    }

    /**
     * @return $this
     */
    public function setKeys(iterable $value)
    {
        $this->keys = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setResult($a, $b)
    {
        foreach ($this->getKeys() as $key => $info) {
            $info = $this->normalizeInfo($info);
            $aValue = $this->fetchValue($a, $key, $info);
            $bValue = $this->fetchValue($b, $key, $info);

            $comparer = $info['comparer'] ?? $this->getDefaultComparer($aValue, $bValue);
            $this->result = $comparer ? $comparer($aValue, $bValue) : $aValue <=> $bValue;
            $this->result *= $info['direction'];

            if ($this->result !== 0) {
                break;
            }
        }

        return $this;
    }

    protected function normalizeInfo($info): array
    {
        if (!is_array($info)) {
            return [
                'comparer' => null,
                'default' => $info,
                'direction' => 1,
            ];
        }

        return $info + [
            'comparer' => null,
            'default' => null,
            'direction' => 1,
        ];
    }

    protected function fetchValue($item, $key, $info)
    {
        return $item instanceof \ArrayAccess ?
            ($item->offsetExists($key) ? $item[$key] : $info['default'])
            : (array_key_exists($key, $item) ? $item[$key] : $info['default']);
    }

    protected function getDefaultComparer($a, $b): ?callable
    {
        $type = $a !== null ? gettype($a) : gettype($b);

        return $type === 'string' ? '\strnatcmp' : null;
    }
}
