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
            if (!is_array($info)) {
                $info = [
                    'comparer' => null,
                    'default' => $info,
                    'direction' => 1,
                ];
            } else {
                $info += [
                    'comparer' => null,
                    'default' => null,
                    'direction' => 1,
                ];
            }

            $aValue = $a instanceof \ArrayAccess ?
                ($a->offsetExists($key) ? $a[$key] : $info['default'])
                : (array_key_exists($key, $a) ? $a[$key] : $info['default']);

            $bValue = $b instanceof \ArrayAccess ?
                ($b->offsetExists($key) ? $b[$key] : $info['default'])
                : (array_key_exists($key, $b) ? $b[$key] : $info['default']);

            $comparer = $info['comparer'] ?? $this->getDefaultComparer($aValue, $bValue);
            $this->result = $comparer ? $comparer($aValue, $bValue) : $aValue <=> $bValue;
            $this->result *= $info['direction'];

            if ($this->result !== 0) {
                break;
            }
        }

        return $this;
    }

    protected function getDefaultComparer($a, $b): ?callable
    {
        $type = $a !== null ? gettype($a) : gettype($b);

        return $type === 'string' ? '\strnatcmp' : null;
    }
}
