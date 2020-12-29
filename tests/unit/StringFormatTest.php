<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\Utils\StringFormat;

/**
 * @covers \Sweetchuck\Utils\StringFormat
 */
class StringFormatTest extends Unit
{

    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesVsprintf(): array
    {
        return [
            'empty args' => [
                'foo',
                'foo',
            ],
            'basic' => [
                '%{b.s}.c',
                '%{a.s}.%{b.s}',
                [
                    'a' => '%{b.s}',
                    'b' => 'c',
                ]
            ],
            'escape - 1' => [
                '%{a.s}.value-a',
                '%%{a.s}.%{a.s}',
                [
                    'a' => 'value-a',
                ]
            ],
            'escape - 2' => [
                '%value-a.value-a',
                '%%%{a.s}.%{a.s}',
                [
                    'a' => 'value-a',
                ]
            ],
        ];
    }

    /**
     * @dataProvider casesVsprintf
     */
    public function testVsprintf(
        string $expected,
        string $format,
        array $args = []
    ): void {
        $this->tester->assertSame(
            $expected,
            StringFormat::vsprintf($format, $args)
        );
    }
}
