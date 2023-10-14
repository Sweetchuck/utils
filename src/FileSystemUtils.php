<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

use Symfony\Component\Filesystem\Path;

class FileSystemUtils
{

    /**
     * @param string $fileName
     * @param string $currentDir
     * @param null|string $rootDir
     *   Do not go above this directory.
     *
     * @return null|string
     *   Returns NULL if the $fileName not exists in any of the parent directories,
     *   returns the parent directory without the $fileName if the $fileName
     *   exists in one of the parent directory.
     */
    public function findFileUpward(
        string $fileName,
        string $currentDir,
        ?string $rootDir = null,
    ): ?string {
        if ($rootDir !== null && !static::isParentDirOrSame($rootDir, $currentDir)) {
            throw new \InvalidArgumentException("The '$rootDir' is not parent dir of '$currentDir'");
        }

        while ($currentDir && ($rootDir === null || static::isParentDirOrSame($rootDir, $currentDir))) {
            if (file_exists("$currentDir/$fileName")) {
                return $currentDir;
            }

            $parentDir = Path::getDirectory($currentDir);
            if ($currentDir === $parentDir) {
                break;
            }

            $currentDir = $parentDir;
        }

        return null;
    }

    public function isParentDirOrSame(string $parentDir, string $childDir): bool
    {
        # @todo Handle a/./b and a/../c formats.
        if ($parentDir === '.') {
            $parentDir = './';
        }

        if ($childDir === '.') {
            $childDir = './';
        }

        $parentDir = preg_replace('@^\./@', '', $parentDir);
        $childDir = preg_replace('@^\./@', '', $childDir);
        $pattern = '@^' . preg_quote($parentDir, '@') . '(/|$)@';

        return (bool) preg_match($pattern, $childDir);
    }

    /**
     * @throws \RuntimeException
     *   When the $filePath is not readable.
     */
    public function fileGetContents(string $filePath): string
    {
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \RuntimeException("File is not readable: '$filePath'", 1);
        }

        return $fileContent;
    }

    /**
     * In this case `php my.php <(echo 'foo')`.
     */
    public function normalizeShellFileDescriptor(string $fileName): string
    {
        return preg_replace(
            '@^/proc/self/fd/(?P<id>\d+)$@',
            'php://fd/$1',
            $fileName,
        );
    }
}
