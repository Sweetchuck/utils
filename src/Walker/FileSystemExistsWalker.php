<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Walker;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @phpstan-import-type SweetchuckUtilsFileSystemExistsWalkerOptions from \Sweetchuck\Utils\Phpstan
 */
class FileSystemExistsWalker
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

    protected Filesystem $fs;

    /**
     * @phpstan-param SweetchuckUtilsFileSystemExistsWalkerOptions $options
     */
    public function setOptions(array $options): static
    {
        if (array_key_exists('baseDir', $options)) {
            $this->setBaseDir($options['baseDir']);
        }

        return $this;
    }

    public function __construct(?Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    public function __invoke(bool &$exists, string $filePath): void
    {
        $this->process($exists, $filePath);
    }

    public function process(bool &$exists, string $filePath): static
    {
        // @todo Wildcard/Glob support.
        $baseDir = $this->getBaseDir();
        $scheme = parse_url($filePath, PHP_URL_SCHEME);
        if (!$scheme && Path::isRelative($filePath) && $baseDir) {
            $filePath = Path::join($baseDir, $filePath);
        }

        $exists = $this->fs->exists($filePath);

        return $this;
    }
}
