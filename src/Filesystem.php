<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

use Webmozart\PathUtil\Path;

class Filesystem
{

    public static function findFileUpward(
        string $fileName,
        string $absoluteDirectory = ''
    ): string {
        if (!$absoluteDirectory) {
            $absoluteDirectory = getcwd();
        }

        while ($absoluteDirectory) {
            if (file_exists("$absoluteDirectory/$fileName")) {
                return $absoluteDirectory;
            }

            $parent = Path::getDirectory($absoluteDirectory);
            if ($parent === $absoluteDirectory) {
                break;
            }

            $absoluteDirectory = $parent;
        }

        return '';
    }
}
