<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\Utils\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * @covers \Sweetchuck\Utils\Filesystem
 */
class FilesystemTest extends Unit
{

    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesFindFileUpward(): array
    {
        return [
            'not-exists' => [
                '',
                'a.txt',
                'foo',
                [
                    'foo' => [],
                ],
            ],
            '0-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                '.',
                [
                    'a.txt' => 'okay',
                ],
            ],
            '1-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                'foo',
                [
                    'a.txt' => 'okay',
                    'foo' => [],
                ],
            ],
            '2-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                'foo/bar',
                [
                    'a.txt' => 'okay',
                    'foo' => [
                        'bar' => [],
                    ],
                ],
            ],
            '2-1' => [
                'vfs://testFindFileUpward/foo',
                'a.txt',
                'foo/bar',
                [
                    'foo' => [
                        'bar' => [],
                        'a.txt' => 'okay',
                    ],
                ],
            ],
            '2-2' => [
                'vfs://testFindFileUpward/foo/bar',
                'a.txt',
                'foo/bar',
                [
                    'foo' => [
                        'bar' => [
                            'a.txt' => 'okay',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesFindFileUpward
     */
    public function testFindFileUpward(
        string $expected,
        string $fileName,
        string $relativeDirectory,
        array $vfsStructure
    ): void {
        $vfs = vfsStream::setup(__FUNCTION__, null, $vfsStructure);
        $absoluteDirectory = Path::join($vfs->url(), $relativeDirectory);

        static::assertEquals(
            $expected,
            Filesystem::findFileUpward($fileName, $absoluteDirectory)
        );
    }
}
