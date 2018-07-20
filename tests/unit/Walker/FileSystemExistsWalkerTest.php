<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Test\Unit\Walker;

use org\bovigo\vfs\vfsStream;
use Sweetchuck\Utils\Walker\FileSystemExistsWalker;

class FileSystemExistsWalkerTest extends \Codeception\Test\Unit
{

    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesWalk(): array
    {
        $structure = [
            'a' => [
                'b.txt' => 'B',
                'c.txt' => 'C',
                'd.txt' => 'D',
            ],
        ];

        return [
            'empty' => [
                [],
                $structure,
                [],
            ],
            'basic' => [
                [
                    'a/a.txt' => false,
                    'a/b.txt' => true,
                    'a/c.txt' => true,
                    'vfs://root/a/d.txt' => true,
                ],
                $structure,
                [
                    'a/a.txt' => false,
                    'a/b.txt' => false,
                    'a/c.txt' => false,
                    'vfs://root/a/d.txt' => false,
                ],
                [
                    'baseDir' => true,
                ]
            ],
        ];
    }

    /**
     * @dataProvider casesWalk
     */
    public function testWalk(array $expected, array $structure, array $items, array $options = []): void
    {
        $vfs = vfsStream::setup('root', 0777, $structure);

        if (isset($options['baseDir']) && $options['baseDir'] === true) {
            $options['baseDir'] = $vfs->url();
        }

        $walker = new FileSystemExistsWalker();
        $walker->setOptions($options);

        array_walk($items, $walker);

        $this->tester->assertSame($expected, $items);
    }
}
