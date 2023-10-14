<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Comparer;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Utils\Comparer\PredefinedComparer;
use Sweetchuck\Utils\Tests\Unit\TestBase;

/**
 * @covers \Sweetchuck\Utils\Comparer\PredefinedComparer
 * @covers \Sweetchuck\Utils\Comparer\ComparerBase
 */
class PredefinedComparerTest extends TestBase
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
                    'weights' => [
                        'a' => 1,
                        'b' => 2,
                        'c' => 3,
                        'd' => 4,
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
    public function testCompare(array $expected, array $items, array $options): void
    {
        $comparer = new PredefinedComparer();
        $comparer->setOptions($options);
        usort($items, $comparer);
        $this->tester->assertSame($expected, $items);
    }
}
