<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\Utils\Filter\ArrayFilterFileSystemExists;

class ArrayFilterFileSystemExistsTest extends Unit
{
    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesCheck(): array
    {
        return [
            'empty' => [
                [],
                [],
                [],
            ],
            'basic' => [
                [
                    0 => 'vfs://basic/a.txt',
                    2 => 'c.txt',
                ],
                [
                    'a.txt' => 'A',
                    'c.txt' => 'C',
                ],
                [
                    'vfs://basic/a.txt',
                    'b.txt',
                    'c.txt',
                ],
                [
                    'baseDir' => 'vfs://basic',
                ]
            ],
        ];
    }

    /**
     * @dataProvider casesCheck
     */
    public function testCheck($expected, array $structure, array $items, array $options = []): void
    {
        vfsStream::setup($this->dataName(), null, $structure);
        $filter = new ArrayFilterFileSystemExists();
        $filter->setOptions($options);
        $this->tester->assertEquals($expected, array_filter($items, $filter));
    }
}
