<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\CoversClass;
use Sweetchuck\Utils\Filter\ArrayAllowedValueFilter;
use Sweetchuck\Utils\Filter\FilterBase;
use Sweetchuck\Utils\Filter\FilterInterface;

#[CoversClass(ArrayAllowedValueFilter::class)]
#[CoversClass(FilterBase::class)]
class ArrayAllowedValueFilterTest extends FilterTestBase
{
    protected function createInstance(): FilterInterface
    {
        return new ArrayAllowedValueFilter();
    }

    /**
     * {@inheritdoc}
     */
    public static function casesIsAllowed(): array
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'basic' => [
                [
                    1 => ['name' => 'a/b'],
                    2 => ['name' => 'a/a'],
                ],
                [
                    ['name' => 'b/a'],
                    ['name' => 'a/b'],
                    ['name' => 'a/a'],
                    ['name' => 'c/a'],
                ],
                [
                    'allowedValues' => [
                        'a/a',
                        'a/b',
                    ],
                ],
            ],
            'property' => [
                [
                    1 => ['id' => 'a/b'],
                    2 => ['id' => 'a/a'],
                ],
                [
                    ['id' => 'b/a'],
                    ['id' => 'a/b'],
                    ['id' => 'a/a'],
                    ['id' => 'c/a'],
                ],
                [
                    'key' => 'id',
                    'allowedValues' => [
                        'a/a',
                        'a/b',
                    ],
                ],
            ],
        ];
    }
}
