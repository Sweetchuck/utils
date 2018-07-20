<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Walker;

use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class FileSystemExistsWalker
{
    /**
     * @var string
     */
    public $baseDir = '.';

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * @return $this
     */
    public function setBaseDir(string $baseDir)
    {
        $this->baseDir = $baseDir;

        return $this;
    }

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    public function __construct(?Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    public function __invoke(bool &$exists, string $filePath)
    {
        $this->process($exists, $filePath);
    }

    /**
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('baseDir', $options)) {
            $this->setBaseDir($options['baseDir']);
        }

        return $this;
    }

    public function process(bool &$exists, string $filePath)
    {
        // @todo Wildcard/Glob support.
        $baseDir = $this->getBaseDir();
        $scheme = parse_url($filePath, PHP_URL_SCHEME);
        if (!$scheme && Path::isRelative($filePath) && $baseDir) {
            $filePath = Path::join($baseDir, $filePath);
        }

        $exists = $this->fs->exists($filePath);
    }
}
