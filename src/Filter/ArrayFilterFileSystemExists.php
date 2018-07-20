<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ArrayFilterFileSystemExists extends ArrayFilterBase
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
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('baseDir', $options)) {
            $this->setBaseDir($options['baseDir']);
        }

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

    /**
     * {@inheritdoc}
     */
    protected function checkDoIt($filePath, ?string $outerKey = null)
    {
        $baseDir = $this->getBaseDir();
        $scheme = parse_url($filePath, PHP_URL_SCHEME);
        if (!$scheme && Path::isRelative($filePath) && $baseDir) {
            $filePath = Path::join($baseDir, $filePath);
        }

        $this->result = $this->fs->exists($filePath);

        return $this;
    }
}
