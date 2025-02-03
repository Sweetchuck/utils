<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Attributes;

use Consolidation\AnnotatedCommand\Parser\CommandInfo;

#[\Attribute(\Attribute::TARGET_METHOD)]
class InitLintReporters
{
    public const SELECTOR = 'app-init-lint-reporters';

    /**
     * @phpstan-param \ReflectionAttribute<\Robo\Tasks> $attribute
     */
    public static function handle(
        \ReflectionAttribute $attribute,
        CommandInfo $commandInfo,
    ): void {
        $commandInfo->addAnnotation(static::SELECTOR, []);
    }
}
