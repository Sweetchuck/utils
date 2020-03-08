<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Filter\ArrayFilterByPropertyValue;

/**
 * @covers \Sweetchuck\Utils\Filter\ArrayFilterByPropertyValue<extended>
 */
class ArrayFilterByPropertyValueTest extends Unit
{
    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    /**
     * @return array
     */
    public function casesCheck(): array
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
                        'a/a' => '',
                        'a/b' => '',
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
                    'property' => 'id',
                    'allowedValues' => [
                        'a/a' => '',
                        'a/b' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesCheck
     */
    public function testCheck(array $expected, array $items, array $options = []): void
    {
        $filter = new ArrayFilterByPropertyValue();
        $filter->setOptions($options);
        $this->tester->assertSame($expected, array_filter($items, $filter));
    }
}
