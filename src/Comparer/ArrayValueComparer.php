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
        foreach ($this->getKeys() as $key => $defaultValue) {
            $aValue = $a instanceof \ArrayAccess ?
                ($a->offsetExists($key) ? $a[$key] : $defaultValue)
                : (array_key_exists($key, $a) ? $a[$key] : $defaultValue);

            $bValue = $b instanceof \ArrayAccess ?
                ($b->offsetExists($key) ? $b[$key] : $defaultValue)
                : (array_key_exists($key, $b) ? $b[$key] : $defaultValue);

            $this->result = $aValue <=> $bValue;

            if ($this->result !== 0) {
                break;
            }
        }

        return $this;
    }
}
