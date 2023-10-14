<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit\Filter;

use Codeception\Attribute\DataProvider;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\Utils\Filter\FileSystemExistsFilter;
use Sweetchuck\Utils\Filter\FilterInterface;

class FileSystemExistsFilterTest extends FilterTestBase
{
    protected function createInstance(): FilterInterface
    {
        return new FileSystemExistsFilter();
    }

    /**
     * {@inheritdoc}
     */
    public static function casesIsAllowed(): array
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
                    'vfs://basic/a.txt',
                    'b.txt',
                    'c.txt',
                ],
                [
                    'baseDir' => 'vfs://basic',
                ],
                [
                    'a.txt' => 'A',
                    'c.txt' => 'C',
                ],
            ],
        ];
    }

    /**
     * @phpstan-param array<mixed> $expected
     * @phpstan-param array<mixed> $items
     * @phpstan-param array<string, mixed> $options
     * @phpstan-param array<string, mixed> $structure
     */
    #[DataProvider('casesIsAllowed')]
    public function testIsAllowed(
        $expected,
        array $items,
        array $options = [],
        array $structure = [],
    ): void {
        vfsStream::setup((string) $this->dataName(), null, $structure);
        parent::testIsAllowed($expected, $items, $options);
    }
}
