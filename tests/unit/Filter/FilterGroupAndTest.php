<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Sweetchuck\Utils\Filter\ArrayAllowedValueFilter;
use Sweetchuck\Utils\Filter\FilterGroupAnd;
use Sweetchuck\Utils\Filter\FilterInterface;

class FilterGroupAndTest extends FilterTestBase
{
    /**
     * {@inheritdoc}
     */
    protected function createInstance(): FilterInterface
    {
        return new FilterGroupAnd();
    }

    /**
     * {@inheritdoc}
     */
    public static function casesIsAllowed(): array
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
                        'k1_is_good_1' => (new ArrayAllowedValueFilter())
                            ->setOptions([
                                'key' => 'k1',
                                'allowedValues' => [
                                    'good_1',
                                ],
                            ]),
                        'k3_is_good_2' => (new ArrayAllowedValueFilter())
                            ->setOptions([
                                'key' => 'k3',
                                'allowedValues' => [
                                    'good_2',
                                ],
                            ]),
                    ],
                ],
            ],
        ];
    }
}
