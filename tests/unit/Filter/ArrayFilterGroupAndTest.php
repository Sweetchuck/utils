<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Filter\ArrayFilterByPropertyValue;
use Sweetchuck\Utils\Filter\ArrayFilterGroupAnd;

class ArrayFilterGroupAndTest extends Unit
{
    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesCheck(): array
    {
        return [
            'basic' => [
                [
                    'd' => [
                        'k1' => 'good_1',
                        'k2' => 'bad',
                        'k3' => 'good_2',
                        'k4' => 'bad',
                    ],
                ],
                [
                    'a' => [
                        'k1' => 'good_1',
                    ],
                    'b' => [
                        'k3' => 'good_2',
                    ],
                    'c' => [
                        'k1' => 'bad',
                        'k2' => 'good_1',
                        'k3' => 'bad',
                        'k4' => 'good_2',
                    ],
                    'd' => [
                        'k1' => 'good_1',
                        'k2' => 'bad',
                        'k3' => 'good_2',
                        'k4' => 'bad',
                    ],
                    'e' => [
                        'k1' => 'good_1',
                        'k2' => 'bad',
                        'k3' => 'bad',
                        'k4' => 'bad',
                    ],
                ],
                [
                    'filters' => [
                        'k1_is_good_1' => (new ArrayFilterByPropertyValue())
                            ->setOptions([
                                'property' => 'k1',
                                'allowedValues' => [
                                    'good_1' => true,
                                ],
                            ]),
                        'k3_is_good_2' => (new ArrayFilterByPropertyValue())
                            ->setOptions([
                                'property' => 'k3',
                                'allowedValues' => [
                                    'good_2' => true,
                                ],
                            ]),
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
        $filter = new ArrayFilterGroupAnd();
        $filter->setOptions($options);
        $this->tester->assertSame($expected, array_filter($items, $filter));
    }
}
