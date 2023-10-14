<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Utils\StringUtils;

/**
 * @phpstan-import-type UrlPasswordFormat from \Sweetchuck\Utils\Phpstan
 *
 * @covers \Sweetchuck\Utils\StringUtils
 */
class StringUtilsTest extends TestBase
{

    /**
     * @return mixed[]
     */
    public static function casesVsprintf(): array
    {
        return [
            'empty args' => [
                'foo',
                'foo',
            ],
            'basic' => [
                '%{b.s}.c',
                '%{a.s}.%{b.s}',
                [
                    'a' => '%{b.s}',
                    'b' => 'c',
                ]
            ],
            'escape - 1' => [
                '%{a.s}.value-a',
                '%%{a.s}.%{a.s}',
                [
                    'a' => 'value-a',
                ]
            ],
            'escape - 2' => [
                '%value-a.value-a',
                '%%%{a.s}.%{a.s}',
                [
                    'a' => 'value-a',
                ]
            ],
        ];
    }

    /**
     * @phpstan-param array<mixed> $args
     */
    #[DataProvider('casesVsprintf')]
    public function testVsprintf(
        string $expected,
        string $format,
        array $args = [],
    ): void {
        $stringUtils = new StringUtils();
        $this->tester->assertSame(
            $expected,
            $stringUtils->vsprintf($format, $args),
        );
    }

    /**
     * @return mixed[]
     */
    public static function casesBuildUri(): array
    {
        return [
            'empty' => [
                '',
                [],
                'raw',
            ],
            'all - query string' => [
                'https://a%3Aa:b%3Ab@c.com:42/d/e.txt?g=h&i=j#f',
                [
                    'scheme' => 'https',
                    'user' => 'a:a',
                    'pass' => 'b:b',
                    'host' => 'c.com',
                    'port' => '42',
                    'path' => '/d/e.txt',
                    'query' => 'g=h&i=j',
                    'fragment' => 'f',
                ],
                'raw',
            ],
            'all - query array' => [
                'https://a%3Aa:b%3Ab@c.com:42/d/e.txt?g=h&i=j#f',
                [
                    'scheme' => 'https',
                    'user' => 'a:a',
                    'pass' => 'b:b',
                    'host' => 'c.com',
                    'port' => '42',
                    'path' => '/d/e.txt',
                    'query' => [
                        'g' => 'h',
                        'i' => 'j',
                    ],
                    'fragment' => 'f',
                ],
                'raw',
            ],
            'password format - raw' => [
                'https://a%3Aa:b%3Ab@c.com',
                [
                    'scheme' => 'https',
                    'user' => 'a:a',
                    'pass' => 'b:b',
                    'host' => 'c.com',
                ],
                'raw',
            ],
            'password format - placeholder' => [
                'https://a%3Aa:****@c.com',
                [
                    'scheme' => 'https',
                    'user' => 'a:a',
                    'pass' => 'b:b',
                    'host' => 'c.com',
                ],
                'placeholder',
            ],
            'password format - hidden' => [
                'https://a%3Aa@c.com',
                [
                    'scheme' => 'https',
                    'user' => 'a:a',
                    'pass' => 'b:b',
                    'host' => 'c.com',
                ],
                'hidden',
            ],
            'path without leading slash' => [
                'https://a.com/foo',
                [
                    'scheme' => 'https',
                    'host' => 'a.com',
                    'path' => 'foo',
                ],
                'raw',
            ],
        ];
    }

    /**
     * @param mixed[] $parts
     *
     * @phpstan-param UrlPasswordFormat $passwordFormat
     */
    #[DataProvider('casesBuildUri')]
    public function testBuildUri(
        string $expected,
        array $parts,
        string $passwordFormat,
    ): void {
        $stringUtils = new StringUtils();
        $this->tester->assertSame(
            $expected,
            $stringUtils->buildUri($parts, $passwordFormat),
        );
    }
}
