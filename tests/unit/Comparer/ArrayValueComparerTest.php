<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Utils\Comparer\ArrayValueComparer;
use Sweetchuck\Utils\Comparer\OrderDirection;
use Sweetchuck\Utils\Tests\Unit\TestBase;

/**
 * @covers \Sweetchuck\Utils\Comparer\ArrayValueComparer
 * @covers \Sweetchuck\Utils\Comparer\ComparerBase
 */
class ArrayValueComparerTest extends TestBase
{

    /**
     * @return mixed[]
     */
    public static function casesCompare(): array
    {
        return [
            'empty' => [
                [],
                [],
                [],
            ],
            'without keys' => [
                [
                    'i2',
                    'i1',
                ],
                [
                    'i2' => ['k2' => 2],
                    'i1' => ['k1' => 1],
                ],
                [],
            ],
            'basic' => [
                [
                    'i1',
                    'i2',
                    'i3',
                    'i4',
                ],
                [
                    'i4' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 4],
                    'i2' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 2],
                    'i1' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 1],
                    'i3' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 3],
                ],
                [
                    'keys' => [
                        'k1' => 0,
                        'k2' => 0,
                        'k3' => 0,
                        'k4' => 0,
                    ],
                ],
            ],
            'basic descending' => [
                [
                    'i4',
                    'i3',
                    'i2',
                    'i1',
                ],
                [
                    'i4' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 4],
                    'i2' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 2],
                    'i1' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 1],
                    'i3' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 3],
                ],
                [
                    'keys' => [
                        'k1' => 0,
                        'k2' => 0,
                        'k3' => 0,
                        'k4' => 0,
                    ],
                    'direction' => -1,
                ],
            ],
            'complex 1' => [
                ['a1', 'a5', 'a9', 'a20'],
                [
                    'a9' => ['k1' => 'a9'],
                    'a5' => [],
                    'a20' => ['k1' => 'a20'],
                    'a1' => ['k1' => 'a1'],
                ],
                [
                    'keys' => [
                        'k1' => [
                            'default' => 'a5',
                        ],
                    ],
                ],
            ],
            'complex 2' => [
                ['a1', 'a5-b', 'a5-a', 'a9', 'a20'],
                [
                    'a9' => ['k1' => 'a9'],
                    'a5-a' => ['k2' => 'a'],
                    'a5-b' => ['k2' => 'b'],
                    'a20' => ['k1' => 'a20'],
                    'a1' => ['k1' => 'a1'],
                ],
                [
                    'keys' => [
                        'k1' => [
                            'default' => 'a5',
                        ],
                        'k2' => [
                            'direction' => OrderDirection::DESC->value,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param mixed[] $expected
     * @param mixed[] $items
     * @param mixed[] $options
     */
    #[DataProvider('casesCompare')]
    public function testCompare(
        array $expected,
        array $items,
        array $options,
    ): void {
        $comparer = new ArrayValueComparer();
        $comparer->setOptions($options);

        $itemsCopy = [];
        foreach ($items as $key => $value) {
            $itemsCopy[$key] = new \ArrayObject($value);
        }

        uasort($items, $comparer);
        $this->tester->assertSame($expected, array_keys($items));

        uasort($itemsCopy, $comparer);
        $this->tester->assertSame($expected, array_keys($items));
    }
}
