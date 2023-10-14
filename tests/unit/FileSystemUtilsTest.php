<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Attribute\DataProvider;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\Utils\FileSystemUtils;
use Symfony\Component\Filesystem\Path;

/**
 * @covers \Sweetchuck\Utils\FileSystemUtils
 */
class FileSystemUtilsTest extends TestBase
{
    protected function createInstance(): FileSystemUtils
    {
        return new FileSystemUtils();
    }

    /**
     * @return mixed[]
     */
    public static function casesFindFileUpward(): array
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
     * @param mixed[] $vfsStructure
     */
    #[DataProvider('casesFindFileUpward')]
    public function testFindFileUpward(
        ?string $expected,
        string $fileName,
        string $currentDir,
        ?string $rootDir,
        array $vfsStructure,
    ): void {
        $vfs = vfsStream::setup(__FUNCTION__, null, $vfsStructure);
        $currentDir = Path::join($vfs->url(), $currentDir);
        if ($rootDir !== null) {
            $rootDir = Path::join($vfs->url(), $rootDir);
        }

        $filesystem = $this->createInstance();
        $this->tester->assertSame(
            $expected,
            $filesystem->findFileUpward($fileName, $currentDir, $rootDir)
        );
    }

    public function testFindFileUpwardNotParent(): void
    {
        $this->tester->expectThrowable(
            \InvalidArgumentException::class,
            function () {
                $filesystem = $this->createInstance();
                $filesystem->findFileUpward('a.txt', '/a', '/b');
            },
        );
    }

    /**
     * @return mixed[]
     */
    public static function casesIsParentDirOrSame(): array
    {
        return [
            'dot dot' => [
                true,
                '.',
                '.',
            ],
            'dot dotSlash' => [
                true,
                '.',
                './',
            ],
            'dotSlash dot' => [
                true,
                './',
                '.',
            ],
            'dotSlash dotSlash' => [
                true,
                './',
                './',
            ],
            'a/b a/b/c' => [
                true,
                'a/b',
                'a/b/c',
            ],
            'a/b/c a/b' => [
                false,
                'a/b/c',
                'a/b',
            ],
            'a/b/c a/b/c' => [
                true,
                'a/b/c',
                'a/b/c',
            ],
            'a/b/c ./a/b/c' => [
                true,
                'a/b/c',
                './a/b/c',
            ],
            './a/b/c a/b/c' => [
                true,
                './a/b/c',
                'a/b/c',
            ],
            './a/b/c ./a/b/c' => [
                true,
                './a/b/c',
                './a/b/c',
            ],
        ];
    }

    #[DataProvider('casesIsParentDirOrSame')]
    public function testIsParentDirOrSame(bool $expected, string $parentDir, string $childDir): void
    {
        $filesystem = $this->createInstance();
        $this->tester->assertSame(
            $expected,
            $filesystem->isParentDirOrSame($parentDir, $childDir),
        );
    }

    /**
     * @return mixed[]
     */
    public static function casesNormalizeShellFileDescriptor(): array
    {
        return [
            'empty' => [
                '',
                '',
            ],
            'regular file' => [
                '/a/b.txt',
                '/a/b.txt',
            ],
            'shell file descriptor' => [
                'php://fd/42',
                '/proc/self/fd/42',
            ],
        ];
    }

    #[DataProvider('casesNormalizeShellFileDescriptor')]
    public function testNormalizeShellFileDescriptor(string $expected, string $fileName): void
    {
        $filesystem = $this->createInstance();
        $this->tester->assertSame(
            $expected,
            $filesystem->normalizeShellFileDescriptor($fileName),
        );
    }
}
