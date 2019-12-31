<?php

declare(strict_types = 1);

namespace Drupal\Tests\marvin\Unit\Filter;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Filter\ArrayFilterEnabled;
use Sweetchuck\Utils\Test\Helper\Dummy\Status;

class ArrayFilterEnabledTest extends Unit
{
    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesCheck(): array
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
        ];

        return [
            'empty' => [
                [],
                [],
            ],
            'basic' => [
                array_intersect_key($items, array_flip(['a', 'c', 'd', 'f', 'h', 'j', 'k', 'l', 'm', 'n'])),
                $items,
            ],
            'inverse' => [
                array_intersect_key($items, array_flip(['b', 'e', 'g', 'i'])),
                $items,
                [
                    'inverse' => true,
                ]
            ],
            'defaultValue: false' => [
                array_intersect_key($items, array_flip(['a', 'd', 'f', 'h'])),
                $items,
                [
                    'defaultValue' => false,
                ],
            ],
            'key: custom; defaultValue: false' => [
                array_intersect_key($items, array_flip(['d', 'h', 'k', 'm'])),
                $items,
                [
                    'key' => 'custom',
                    'defaultValue' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesCheck
     */
    public function testCheck(array $expected, array $items, array $options = []): void
    {
        $filter = new ArrayFilterEnabled();
        $filter->setOptions($options);
        $this->assertSame($expected, array_filter($items, $filter));
    }
}
