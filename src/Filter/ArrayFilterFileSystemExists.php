<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Filter;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ArrayFilterFileSystemExists extends ArrayFilterBase
{
    public string $baseDir = '.';

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

    protected Filesystem $fs;

    public function __construct(?Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function checkDoIt($item, ?string $outerKey = null)
    {
        $baseDir = $this->getBaseDir();
        $scheme = parse_url($item, PHP_URL_SCHEME);
        if (!$scheme && Path::isRelative($item) && $baseDir) {
            $item = Path::join($baseDir, $item);
        }

        $this->result = $this->fs->exists($item);

        return $this;
    }
}
