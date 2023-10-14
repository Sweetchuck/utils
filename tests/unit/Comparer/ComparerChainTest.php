<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Sweetchuck\Utils\Comparer\ArrayValueComparer;
use Sweetchuck\Utils\Comparer\ComparerGroup;
use Sweetchuck\Utils\Tests\Unit\TestBase;

/**
 * @covers \Sweetchuck\Utils\Comparer\ComparerGroup
 * @covers \Sweetchuck\Utils\Comparer\ComparerBase
 */
class ComparerChainTest extends TestBase
{

    public function testCompare(): void
    {
        $comparer1Options = [
            'keys' => [
                'a' => 0,
            ],
        ];
        $comparer1 = new ArrayValueComparer();
        $comparer1->setOptions($comparer1Options);

        $comparer2Options = [
            'keys' => [
                'b' => 0,
            ],
        ];
        $comparer2 = new ArrayValueComparer();
        $comparer2->setOptions($comparer2Options);

        $expected = [
            'i11' => ['a' => 1, 'b' => 1],
            'i12' => ['a' => 1, 'b' => 2],
            'i13' => ['a' => 1, 'b' => 3],
            'i21' => ['a' => 2, 'b' => 1],
            'i22' => ['a' => 2, 'b' => 2],
            'i23' => ['a' => 2, 'b' => 3],
            'i31' => ['a' => 3, 'b' => 1],
            'i32' => ['a' => 3, 'b' => 2],
            'i33' => ['a' => 3, 'b' => 3],
        ];

        $items = [
            'i23' => ['a' => 2, 'b' => 3],
            'i21' => ['a' => 2, 'b' => 1],
            'i13' => ['a' => 1, 'b' => 3],
            'i33' => ['a' => 3, 'b' => 3],
            'i12' => ['a' => 1, 'b' => 2],
            'i31' => ['a' => 3, 'b' => 1],
            'i22' => ['a' => 2, 'b' => 2],
            'i11' => ['a' => 1, 'b' => 1],
            'i32' => ['a' => 3, 'b' => 2],
        ];

        $itemsCopy = [];
        foreach ($items as $key => $value) {
            $itemsCopy[$key] = new \ArrayObject($value);
        }

        $comparer = new ComparerGroup();
        $comparer->addComparers([
            'a' => $comparer1,
            'b' => $comparer2,
        ]);

        uasort($items, $comparer);
        $this->tester->assertSame($expected, $items);

        uasort($itemsCopy, $comparer);
        $this->tester->assertSame($expected, $items);
    }
}
