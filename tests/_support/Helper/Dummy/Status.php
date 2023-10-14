<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Helper\Dummy;

use Sweetchuck\Utils\EnabledInterface;

class Status implements EnabledInterface
{
    /**
     * @var bool
     */
    protected $enabled = true;

    public function __construct(bool $status)
    {
        $this->enabled = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
