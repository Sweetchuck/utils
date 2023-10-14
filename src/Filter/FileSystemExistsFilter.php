<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @phpstan-import-type SweetchuckUtilsFileSystemExistsFilterOptions from \Sweetchuck\Utils\Phpstan
 *
 * @template TItem
 *
 * @extends \Sweetchuck\Utils\Filter\FilterBase<TItem>
 */
class FileSystemExistsFilter extends FilterBase
{
    public string $baseDir = '.';

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function setBaseDir(string $baseDir): static
    {
        $this->baseDir = $baseDir;

        return $this;
    }

    /**
     * @phpstan-param SweetchuckUtilsFileSystemExistsFilterOptions $options
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        if (array_key_exists('baseDir', $options)) {
            $this->setBaseDir($options['baseDir']);
        }

        return $this;
    }

    protected Filesystem $fs;

    public function __construct(?Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function setResult(mixed $item, null|int|string $outerKey = null): static
    {
        // @todo Add support for \SplFileInfo.
        $baseDir = $this->getBaseDir();
        $scheme = parse_url($item, PHP_URL_SCHEME);
        if (!$scheme && Path::isRelative($item) && $baseDir) {
            $item = Path::join($baseDir, $item);
        }

        $this->result = $this->fs->exists($item);

        return $this;
    }
}
