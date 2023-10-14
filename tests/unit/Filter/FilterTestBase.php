<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Utils\Filter\FilterInterface;
use Sweetchuck\Utils\Tests\Unit\TestBase;

abstract class FilterTestBase extends TestBase
{
    /**
     * @phpstan-return array<string, mixed>
     */
    abstract public static function casesIsAllowed(): array;

    /**
     * @phpstan-return \Sweetchuck\Utils\Filter\FilterInterface<mixed>
     */
    abstract protected function createInstance(): FilterInterface;

    /**
     * @phpstan-param array<mixed> $expected
     * @phpstan-param array<mixed> $items
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProvider('casesIsAllowed')]
    public function testIsAllowed(
        array $expected,
        array $items,
        array $options = [],
    ): void {
        $filter = $this->createInstance();
        $filter->setOptions($options);
        $this->tester->assertSame($expected, array_filter($items, $filter));
    }
}
