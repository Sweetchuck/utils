<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Comparer\PredefinedComparer;
use Sweetchuck\Utils\Test\UnitTester;

/**
 * @covers \Sweetchuck\Utils\Comparer\PredefinedComparer<extended>
 */
class PredefinedComparerTest extends Unit
{
    protected UnitTester $tester;

    public function casesCompare(): array
    {
        return [
            'empty' => [
                [],
                [],
                [],
                0,
            ],
            'basic' => [
                [
                    'a',
                    'b',
                    'c',
                    'd',
                ],
                [
                    'd',
                    'b',
                    'a',
                    'c',
                ],
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ],
                0,
            ],
        ];
    }

    /**
     * @dataProvider casesCompare
     */
    public function testCompare(array $expected, array $items, array $weights, int $defaultWeight)
    {
        $comparer = new PredefinedComparer($weights, $defaultWeight);
        usort($items, $comparer);
        $this->tester->assertSame($expected, $items);
    }
}
