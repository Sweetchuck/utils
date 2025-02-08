<?php

declare(strict_types = 1);


namespace Sweetchuck\Utils\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\CoversClass;
use Sweetchuck\Utils\Filter\CustomFilter;
use Sweetchuck\Utils\Filter\CustomFilterHelper;
use Sweetchuck\Utils\Filter\FilterInterface;

#[CoversClass(CustomFilter::class)]
#[CoversClass(CustomFilterHelper::class)]
class CustomFilterTest extends FilterTestBase
{

    protected function createInstance(): FilterInterface
    {
        return new CustomFilter();
    }

    public static function casesIsAllowed(): array
    {
        $cases = [];
        $cases['empty'] = [
            [],
            [],
        ];
        $cases['no-arg-collector no-operator normal'] = [
            [
                'a' => true,
                'c' => true,
            ],
            [
                'a' => true,
                'b' => false,
                'c' => true,
            ],
        ];
        $cases['no-arg-collector no-operator inverse'] = [
            [
                'b' => false,
            ],
            [
                'a' => true,
                'b' => false,
                'c' => true,
            ],
            [
                'inverse' => true,
            ],
        ];
        $cases['no-arg-collector with-operator is_int'] = [
            [
                'a' => 42,
                'c' => 44,
            ],
            [
                'a' => 42,
                'b' => 43.5,
                'c' => 44,
            ],
            [
                'operator' => is_int(...),
            ],
        ];
        $cases['with-arg-collector with-operator lessThanOrEqual'] = [
            [
                'a' => ['l' => 1, 'r' => 2],
                'b' => ['l' => 2, 'r' => 2],
            ],
            [
                'a' => ['l' => 1, 'r' => 2],
                'b' => ['l' => 2, 'r' => 2],
                'c' => ['l' => 3, 'r' => 2],
            ],
            [
                'argCollector' => function (array $item): array {
                    return [
                        $item['l'],
                        $item['r'],
                    ];
                },
                'operator' => CustomFilterHelper::lessThanOrEqual(...),
            ],
        ];

        return $cases;
    }
}
