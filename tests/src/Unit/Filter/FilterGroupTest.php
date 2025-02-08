<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sweetchuck\Utils\Filter\FilterGroup;
use Sweetchuck\Utils\Filter\FilterGroupBase;
use Sweetchuck\Utils\Tests\Unit\TestBase;

#[CoversClass(FilterGroup::class)]
#[CoversClass(FilterGroupBase::class)]
class FilterGroupTest extends TestBase
{

    #[Test]
    public function testSetOptions(): void
    {
        $group = new FilterGroup();
        $group->setOptions([
            'type' => 'AND',
        ]);
        $this->assertSame('AND', $group->getType());

        $group->setOptions([
            'type' => 'OR',
        ]);
        $this->assertSame('OR', $group->getType());
    }
}
