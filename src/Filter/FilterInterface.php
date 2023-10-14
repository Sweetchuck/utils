<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @phpstan-import-type SweetchuckUtilsFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 */
interface FilterInterface
{

    public function getInverse(): bool;

    public function setInverse(bool $value): static;

    /**
     * @phpstan-param SweetchuckUtilsFilterOptions $options
     */
    public function setOptions(array $options): static;

    /**
     * @phpstan-param TItem $item
     */
    public function __invoke(mixed $item, null|int|string $outerKey = null): bool;

    /**
     * @phpstan-param TItem $item
     */
    public function isAllowed(mixed $item, null|int|string $outerKey = null): bool;
}
