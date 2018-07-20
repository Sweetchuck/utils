<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

class ArrayValueComparer extends BaseComparer
{

    public function __construct(array $keys = [])
    {
        $this->setKeys($keys);
    }

    /**
     * @var array
     */
    protected $keys = [];

    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @return $this
     */
    public function setKeys(array $value)
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
            $aValue = array_key_exists($key, $a) ? $a[$key] : $defaultValue;
            $bValue = array_key_exists($key, $b) ? $b[$key] : $defaultValue;

            $this->result = $aValue <=> $bValue;

            if ($this->result !== 0) {
                break;
            }
        }

        return $this;
    }
}
