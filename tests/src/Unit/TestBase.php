<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{

    protected function selfProjectRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    protected function createTempDir(): string
    {
        $dir = $this->randomTempDirName();
        mkdir($dir, 0777 - umask(), true);

        return $dir;
    }

    protected function randomTempDirName(): string
    {
        return implode('/', [
            sys_get_temp_dir(),
            'sweetchuck',
            'composer-suite',
            'test-' . $this->randomId(),
        ]);
    }

    protected function randomId(): string
    {
        return md5((string) (microtime(true) * rand(0, 10000)));
    }
}
