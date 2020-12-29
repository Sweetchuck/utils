<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Comparer\ArrayValueComparer;

class ArrayValueComparerTest extends Unit
{

    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

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
                    'i2' => ['k2' => 2],
                    'i1' => ['k1' => 1],
                ],
                [
                    'i2' => ['k2' => 2],
                    'i1' => ['k1' => 1],
                ],
                [],
            ],
            'basic' => [
                [
                    'i1' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 1],
                    'i2' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 2],
                    'i3' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 3],
                    'i4' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 4],
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
                    'i4' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 4],
                    'i3' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 3],
                    'i2' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 2],
                    'i1' => ['k1' => 1, 'k2' => 1, 'k3' => 1, 'k4' => 1],
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

        uasort($items, $comparer);
        $this->tester->assertSame($expected, $items);
    }
}
