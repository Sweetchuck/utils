<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Walker;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sweetchuck\Utils\Tests\Unit\TestBase;
use Sweetchuck\Utils\Walker\FileSystemExistsWalker;

#[CoversClass(FileSystemExistsWalker::class)]
class FileSystemExistsWalkerTest extends TestBase
{

    /**
     * @return mixed[]
     */
    public static function casesWalk(): array
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
     * @param mixed[] $expected
     * @param mixed[] $structure
     * @param mixed[] $items
     * @param mixed[] $options
     */
    #[DataProvider('casesWalk')]
    public function testWalk(array $expected, array $structure, array $items, array $options = []): void
    {
        $vfs = vfsStream::setup('root', 0777, $structure);

        if (isset($options['baseDir']) && $options['baseDir'] === true) {
            $options['baseDir'] = $vfs->url();
        }

        $walker = new FileSystemExistsWalker();
        $walker->setOptions($options);

        array_walk($items, $walker);

        static::assertSame($expected, $items);
    }
}
