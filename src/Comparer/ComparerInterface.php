<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

/**
 * @phpstan-import-type OrderDirectionMixed from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type ComparisonResultValue from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type SweetchuckUtilsComparer from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type SweetchuckUtilsComparerOptions from \Sweetchuck\Utils\Phpstan
 * @phpstan-import-type SweetchuckUtilsComparerChainOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 */
interface ComparerInterface
{
    public function getDirection(): OrderDirection;

    /**
     * @phpstan-param OrderDirectionMixed $direction
     */
    public function setDirection(int|string|OrderDirection $direction): static;

    /**
     * @phpstan-param SweetchuckUtilsComparerOptions $options
     */
    public function setOptions(array $options): static;

    /**
     * @phpstan-param TItem $a
     * @phpstan-param TItem $b
     *
     * @phpstan-return ComparisonResultValue
     */
    public function __invoke($a, $b): int;

    /**
     * @phpstan-param TItem $a
     * @phpstan-param TItem $b
     *
     * @phpstan-return ComparisonResultValue
     */
    public function compare($a, $b): int;
}
