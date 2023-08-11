<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\Utils\Uri;

/**
 * @covers \Sweetchuck\Utils\Uri
 */
class UriTest extends Unit
{

    /**
     * @var \Sweetchuck\Utils\Test\UnitTester
     */
    protected $tester;

    public function casesBuild(): array
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
     * @dataProvider casesBuild
     */
    public function testBuild(
        string $expected,
        array $parts,
        string $passwordFormat
    ): void {
        $this->tester->assertSame(
            $expected,
            Uri::build($parts, $passwordFormat)
        );
    }
}
