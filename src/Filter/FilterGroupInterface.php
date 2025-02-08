<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @todo XOR.
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterInterface<TItem>
 */
interface FilterGroupInterface extends FilterInterface, \Countable
{

    /**
     * @return callable[]
     */
    public function getFilters(): array;

    /**
     * @param callable[] $filters
     */
    public function setFilters(array $filters): static;

    public function setFilter(int|string $name, ?callable $filter): static;

    public function getFilter(int|string $name): ?callable;

    public function addFilter(int|string $name, callable $filter): static;

    /**
     * @param callable[] $filters
     */
    public function addFilters(array $filters): static;

    public function removeFilter(int|string $name): static;

    /**
     * @throws \Throwable
     *
     * @todo Consider to move this into FilterInterface.
     */
    public function validate(): static;
}
