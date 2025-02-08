<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sweetchuck\Utils\Filter\CustomFilterHelper;
use Sweetchuck\Utils\Tests\Unit\TestBase;

#[CoversClass(CustomFilterHelper::class)]
class CustomFilterHelperTest extends TestBase
{

    /**
     * @return array<array{expected: string, name: string}>
     */
    public static function casesNormalizeOperator(): array
    {
        return [
            ['expected' => 'unknown', 'name' => 'unknown'],

            ['expected' => '<', 'name' => 'lt'],
            ['expected' => '<', 'name' => 'less'],
            ['expected' => '<', 'name' => 'lessThan'],
            ['expected' => '<', 'name' => 'less_than'],

            ['expected' => '<=', 'name' => 'le'],
            ['expected' => '<=', 'name' => 'lessOrEqual'],
            ['expected' => '<=', 'name' => 'lessThanOrEqual'],
            ['expected' => '<=', 'name' => 'less_or_equal'],
            ['expected' => '<=', 'name' => 'less_than_or_equal'],

            ['expected' => '==', 'name' => '='],
            ['expected' => '==', 'name' => 'eq'],
            ['expected' => '==', 'name' => 'equal'],
            ['expected' => '==', 'name' => 'equalLazy'],
            ['expected' => '==', 'name' => 'equal_lazy'],

            ['expected' => '===', 'name' => 'equalStrict'],
            ['expected' => '===', 'name' => 'equal_strict'],

            ['expected' => '>=', 'name' => 'gtOrEq'],
            ['expected' => '>=', 'name' => 'gt_or_eq'],
            ['expected' => '>=', 'name' => 'greaterThanOrEqual'],
            ['expected' => '>=', 'name' => 'greater_than_or_equal'],
            ['expected' => '>=', 'name' => 'greaterOrEqual'],
            ['expected' => '>=', 'name' => 'greater_or_equal'],

            ['expected' => '>', 'name' => 'gt'],
            ['expected' => '>', 'name' => 'greater'],
            ['expected' => '>', 'name' => 'greaterThan'],
            ['expected' => '>', 'name' => 'greater_than'],
        ];
    }

    #[Test]
    #[DataProvider('casesNormalizeOperator')]
    public function testNormalizeOperator(string $expected, string $name): void
    {
        $this->assertSame(
            $expected,
            CustomFilterHelper::normalizeOperator($name),
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorEmpty(): array
    {
        return [
            'empty.0' => ['expected' => true,  'operator' => 'empty', 'args' => [null]],
            'empty.1' => ['expected' => false, 'operator' => 'empty', 'args' => [true]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorIsSet(): array
    {
        return [
            'isset.0' => ['expected' => false, 'operator' => 'isset', 'args' => [null]],
            'isset.1' => ['expected' => true,  'operator' => 'isset', 'args' => ['']],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorInstanceOf(): array
    {
        return [
            'instanceOf.1' => [
                'expected' => true,
                'operator' => 'instanceOf',
                'args' => [new \stdClass(), \stdClass::class],
            ],
            'instanceOf.0' => [
                'expected' => false,
                'operator' => 'instanceOf',
                'args' => [new \stdClass(), \Countable::class],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorLessThan(): array
    {
        return [
            'lessThan.<'  => ['expected' => true,  'operator' => '<', 'args' => [1, 2]],
            'lessThan.==' => ['expected' => false, 'operator' => '<', 'args' => [1, 1]],
            'lessThan.>'  => ['expected' => false, 'operator' => '<', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorLessThanOrEqual(): array
    {
        return [
            'lessThanOrEqual.<'  => ['expected' => true,  'operator' => '<=', 'args' => [1, 2]],
            'lessThanOrEqual.==' => ['expected' => true,  'operator' => '<=', 'args' => [1, 1]],
            'lessThanOrEqual.>'  => ['expected' => false, 'operator' => '<=', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorEqual(): array
    {
        return [
            'equal.<'  => ['expected' => false, 'operator' => '==', 'args' => [1, 2]],
            'equal.=+' => ['expected' => true,  'operator' => '==', 'args' => [1, 1]],
            'equal.=-' => ['expected' => true,  'operator' => '==', 'args' => [1, '1']],
            'equal.>'  => ['expected' => false, 'operator' => '==', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorEqualStrict(): array
    {
        return [
            'equalStrict.<'  => ['expected' => false, 'operator' => '===', 'args' => [1, 2]],
            'equalStrict.=+' => ['expected' => true,  'operator' => '===', 'args' => [1, 1]],
            'equalStrict.=-' => ['expected' => false, 'operator' => '===', 'args' => [1, '1']],
            'equalStrict.>'  => ['expected' => false, 'operator' => '===', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorGreaterThanOrEqual(): array
    {
        return [
            'greaterThanOrEqual.<' => ['expected' => false, 'operator' => '>=', 'args' => [1, 2]],
            'greaterThanOrEqual.=' => ['expected' => true,  'operator' => '>=', 'args' => [1, 1]],
            'greaterThanOrEqual.>' => ['expected' => true,  'operator' => '>=', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorGreater(): array
    {
        return [
            'greater.<' => ['expected' => false, 'operator' => '>', 'args' => [1, 2]],
            'greater.=' => ['expected' => false, 'operator' => '>', 'args' => [1, 1]],
            'greater.>' => ['expected' => true,  'operator' => '>', 'args' => [2, 1]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function casesOperatorBetween(): array
    {
        return [
            'between.lower' => [
                'expected' => false,
                'operator' => 'between',
                'args' => [1, 2, 4],
            ],
            'between.on min border >=' => [
                'expected' => true,
                'operator' => 'between',
                'args' => [2, 2, 4],
            ],
            'between.on min border >' => [
                'expected' => false,
                'operator' => 'between',
                'args' => [2, 2, 4, '>'],
            ],
            'between.in' => [
                'expected' => true,
                'operator' => 'between',
                'args' => [3, 2, 4],
            ],
            'between.on max border <=' => [
                'expected' => true,
                'operator' => 'between',
                'args' => [4, 2, 4],
            ],
            'between.on max border <' => [
                'expected' => false,
                'operator' => 'between',
                'args' => [4, 2, 4, '>=', '<'],
            ],
            'between.higher' => [
                'expected' => false,
                'operator' => 'between',
                'args' => [5, 2, 4],
            ],
        ];
    }

    /**
     * @phpstan-param array<mixed> $args
     */
    #[Test]
    #[DataProvider('casesOperatorEmpty')]
    #[DataProvider('casesOperatorIsSet')]
    #[DataProvider('casesOperatorInstanceOf')]
    #[DataProvider('casesOperatorLessThan')]
    #[DataProvider('casesOperatorLessThanOrEqual')]
    #[DataProvider('casesOperatorEqual')]
    #[DataProvider('casesOperatorEqualStrict')]
    #[DataProvider('casesOperatorGreaterThanOrEqual')]
    #[DataProvider('casesOperatorGreater')]
    #[DataProvider('casesOperatorBetween')]
    public function testOperatorLessThan(bool $expected, string $operator, array $args): void
    {
        $operator = CustomFilterHelper::createFromOperator($operator);
        $this->assertSame($expected, $operator(...$args));
    }
}
