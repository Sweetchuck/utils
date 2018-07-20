<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Sweetchuck\Utils\ArrayFilterInterface;

abstract class ArrayFilterBase implements ArrayFilterInterface
{

    /**
     * @var bool
     */
    protected $inverse = false;

    /**
     * {@inheritdoc}
     */
    public function getInverse(): bool
    {
        return $this->inverse;
    }

    /**
     * {@inheritdoc}
     */
    public function setInverse(bool $value)
    {
        $this->inverse = $value;

        return $this;
    }

    /**
     * @var bool
     */
    protected $result = true;

    /**
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('inverse', $options)) {
            $this->setInverse($options['inverse']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($item, ?string $outerKey = null): bool
    {
        return $this->check($item, $outerKey);
    }

    /**
     * {@inheritdoc}
     */
    public function check($item, ?string $outerKey = null): bool
    {
        return $this
            ->checkDoIt($item, $outerKey)
            ->checkReturn();
    }

    /**
     * @return $this
     */
    abstract protected function checkDoIt($item, ?string $outerKey = null);

    protected function checkReturn(): bool
    {
        return $this->getInverse() ? !$this->result : (bool) $this->result;
    }
}
