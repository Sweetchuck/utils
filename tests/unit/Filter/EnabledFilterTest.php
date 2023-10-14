<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Sweetchuck\Utils\Filter\EnabledFilter;
use Sweetchuck\Utils\Filter\FilterInterface;
use Sweetchuck\Utils\Tests\Helper\Dummy\Status;

class EnabledFilterTest extends FilterTestBase
{
    /**
     * {@inheritdoc}
     */
    protected function createInstance(): FilterInterface
    {
        return new EnabledFilter();
    }

    /**
     * {@inheritdoc}
     */
    public static function casesIsAllowed(): array
    {
        $items = [
            'a' => [
                'enabled' => true,
            ],
            'b' => [
                'enabled' => false,
            ],
            'c' => [],
            'd' => true,
            'e' => false,
            'f' => (object) ['enabled' => true],
            'g' => (object) ['enabled' => false],
            'h' => new Status(true),
            'i' => new Status(false),
            'j' => 'nothing',
            'k' => [
                'custom' => true,
            ],
            'l' => [
                'custom' => false,
            ],
            'm' => (object) ['custom' => true],
            'n' => (object) ['custom' => false],
            'o' => '0',
            'p' => 'true',
            'q' => 'false',
        ];

        return [
            'empty' => [
                [],
                [],
            ],
            'basic' => [
                array_intersect_key($items, array_flip(['a', 'c', 'd', 'f', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q'])),
                $items,
            ],
            'inverse' => [
                array_intersect_key($items, array_flip(['b', 'e', 'g', 'i', 'o'])),
                $items,
                [
                    'inverse' => true,
                ],
            ],
            'defaultValue: false' => [
                array_intersect_key($items, array_flip(['a', 'd', 'f', 'h', 'j', 'p', 'q'])),
                $items,
                [
                    'defaultValue' => false,
                ],
            ],
            'key: custom; defaultValue: false' => [
                array_intersect_key($items, array_flip(['d', 'h', 'k', 'm', 'j', 'p', 'q'])),
                $items,
                [
                    'key' => 'custom',
                    'defaultValue' => false,
                ],
            ],
            'string mapping' => [
                array_intersect_key($items, array_flip(['a', 'c', 'd', 'f', 'h', 'j', 'k', 'l', 'm', 'n', 'o', 'p'])),
                $items,
                [
                    'stringToBool' => [
                        '0' => true,
                        'false' => false,
                    ],
                ],
            ],
        ];
    }
}
