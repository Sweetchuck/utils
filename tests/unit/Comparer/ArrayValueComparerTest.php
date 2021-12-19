<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Comparer\ArrayValueComparer;
use Sweetchuck\Utils\Test\UnitTester;

/**
 * @covers \Sweetchuck\Utils\Comparer\ArrayValueComparer<extended>
 */
class ArrayValueComparerTest extends Unit
{

    protected UnitTester $tester;

    public function casesCompare(): array
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
                ['k1' => 0, 'k2' => 0, 'k3' => 0, 'k4' => 0],
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
                ['k1' => 0, 'k2' => 0, 'k3' => 0, 'k4' => 0],
                false,
            ],
        ];
    }

    /**
     * @dataProvider casesCompare
     */
    public function testCompare(
        array $expected,
        array $items,
        array $keys,
        ?bool $ascending = null
    ): void {
        $comparer = new ArrayValueComparer($keys);
        if ($ascending !== null) {
            $comparer->setAscending($ascending);
        }

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
