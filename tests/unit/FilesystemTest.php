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
                null,
                'a.txt',
                'foo',
                null,
                [
                    'foo' => [],
                ],
            ],
            '0-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                '.',
                null,
                [
                    'a.txt' => 'okay',
                ],
            ],
            '1-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                'foo',
                null,
                [
                    'a.txt' => 'okay',
                    'foo' => [],
                ],
            ],
            '2-0' => [
                'vfs://testFindFileUpward',
                'a.txt',
                'foo/bar',
                null,
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
                null,
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
                null,
                [
                    'foo' => [
                        'bar' => [
                            'a.txt' => 'okay',
                        ],
                    ],
                ],
            ],
            'multiple' => [
                'vfs://testFindFileUpward/foo/bar',
                'a.txt',
                'foo/bar/baz',
                null,
                [
                    'foo' => [
                        'bar' => [
                            'baz' => [],
                            'a.txt' => 'okay',
                        ],
                        'a.txt' => 'okay',
                    ],
                    'a.txt' => 'okay',
                ],
            ],
            'with root dir' => [
                null,
                'a.txt',
                'foo/bar/baz/abc',
                'foo/bar',
                [
                    'foo' => [
                        'bar' => [
                            'baz' => [
                                'abc' =>[],
                            ],
                        ],
                        'a.txt' => 'okay',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesFindFileUpward
     */
    public function testFindFileUpward(
        ?string $expected,
        string $fileName,
        string $currentDir,
        ?string $rootDir,
        array $vfsStructure
    ): void {
        $vfs = vfsStream::setup(__FUNCTION__, null, $vfsStructure);
        $currentDir = Path::join($vfs->url(), $currentDir);
        if ($rootDir !== null) {
            $rootDir = Path::join($vfs->url(), $rootDir);
        }

        $this->tester->assertSame(
            $expected,
            Filesystem::findFileUpward($fileName, $currentDir, $rootDir)
        );
    }

    public function testFindFileUpwardNotParent()
    {
        $this->tester->expectThrowable(
            \InvalidArgumentException::class,
            function () {
                Filesystem::findFileUpward('a.txt', '/a', '/b');
            },
        );
    }
}
