<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsCustomFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 */
class CustomFilter extends FilterBase
{

    /**
     * @var null|callable
     */
    protected $argCollector = null;

    public function getArgCollector(): ?callable
    {
        return $this->argCollector;
    }

    public function setArgCollector(?callable $argCollector): static
    {
        $this->argCollector = $argCollector;

        return $this;
    }

    /**
     * @var null|callable
     */
    protected $operator = null;

    public function getOperator(): ?callable
    {
        return $this->operator;
    }

    public function setOperator(?callable $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsCustomFilterOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('argCollector', $options)) {
            $this->setArgCollector($options['argCollector']);
        }

        if (array_key_exists('operator', $options)) {
            $this->setOperator($options['operator']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult(mixed $item, null|int|string $outerKey = null): static
    {
        $argCollector = $this->getArgCollector();
        $args = $argCollector ?
            $argCollector($item, $outerKey)
            : [$item];

        $operator = $this->getOperator();
        if ($operator) {
            $this->result = $operator(...$args);

            return $this;
        }

        $argCount = count($args);
        if ($argCount === 0) {
            $this->result = false;

            return $this;
        }

        if ($argCount === 1 || !$argCollector) {
            $this->result = (bool) reset($args);

            return $this;
        }

        $this->result = $args[0] == $args[1];

        return $this;
    }
}
