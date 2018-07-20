<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

interface EnabledInterface
{
    public function isEnabled(): bool;
}
