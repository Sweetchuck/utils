<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

/**
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterGroupBase<TItem>
 */
class FilterGroupOr extends FilterGroupBase
{
    protected string $type = 'OR';
}
