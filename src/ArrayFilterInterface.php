<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

interface ArrayFilterInterface
{

    public function getInverse(): bool;

    /**
     * @return $this
     */
    public function setInverse(bool $value);

    /**
     * @param array|\ArrayAccess $item
     * @param null|string $outerKey
     *
     * @return bool
     */
    public function __invoke($item, ?string $outerKey = null): bool;

    /**
     * @param array|\ArrayAccess|\Sweetchuck\Utils\EnabledInterface $item
     * @param null|string $outerKey
     *
     * @return bool
     */
    public function check($item, ?string $outerKey = null): bool;
}
