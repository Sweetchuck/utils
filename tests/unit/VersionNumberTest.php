<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Tests\Unit;

use Codeception\Test\Unit;
use PHPUnit\Framework\Exception as PHPUnitException;
use Sweetchuck\Utils\Test\UnitTester;
use Sweetchuck\Utils\VersionNumber;

/**
 * @covers \Sweetchuck\Utils\VersionNumber
 */
class VersionNumberTest extends Unit
{

    protected UnitTester $tester;

    public function testMagicGet()
    {
        $version = VersionNumber::createFromString('1.2.3-beta4+foo');

        $this->tester->assertFalse(isset($version->formatNOPE));
        $this->tester->assertTrue(isset($version->formatMA0DMI0));

        $this->tester->assertSame('010203', $version->formatMA2MI2P2);

        try {
            $this->tester->assertIsString($version->formatNOPE);
            $this->fail('Where is the exception?');
        } catch (PHPUnitException $e) {
            $this->tester->assertSame(1024, $e->getCode());
            $this->tester->assertRegExp(
                // This error message differs in different PHP versions.
                implode(' ', [
                    '@^Undefined property via __get\(\): formatNOPE',
                    'in .+?/tests/unit/VersionNumberTest\.php',
                    'on line \d+\b@',
                ]),
                $e->getMessage(),
            );
        }
    }

    public function casesTransformation(): array
    {
        return [
            'major' => [
                'string' => '7',
                'array' => [
                    'major' => '7',
                ],
            ],
            'major-preRelease' => [
                'string' => '7-rc1',
                'array' => [
                    'major' => '7',
                    'preRelease' => 'rc1',
                ],
            ],
            'major+metadata' => [
                'string' => '7+2020-01-01',
                'array' => [
                    'major' => '7',
                    'metadata' => '2020-01-01',
                ],
            ],
            'major-preRelease+metadata' => [
                'string' => '7-rc1+2020-01-01',
                'array' => [
                    'major' => '7',
                    'preRelease' => 'rc1',
                    'metadata' => '2020-01-01',
                ],
            ],
            'major.minor' => [
                'string' => '7.3',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                ],
            ],
            'major.minor-preRelease' => [
                'string' => '7.3-rc1',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'preRelease' => 'rc1',
                ],
            ],
            'major.minor+metadata' => [
                'string' => '7.3+2020-01-01',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'metadata' => '2020-01-01',
                ],
            ],
            'major.minor-preRelease+metadata' => [
                'string' => '7.3-rc1+2020-01-01',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'preRelease' => 'rc1',
                    'metadata' => '2020-01-01',
                ],
            ],
            'major.minor.patch' => [
                'string' => '7.3.9',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'patch' => '9',
                ],
            ],
            'major.minor.patch-preRelease' => [
                'string' => '7.3.9-rc1',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'patch' => '9',
                    'preRelease' => 'rc1',
                ],
            ],
            'major.minor.patch+metadata' => [
                'string' => '7.3.9+2020-01-01',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'patch' => '9',
                    'metadata' => '2020-01-01',
                ],
            ],
            'major.minor.patch-preRelease+metadata' => [
                'string' => '7.3.21-rc2+2020-01-01',
                'array' => [
                    'major' => '7',
                    'minor' => '3',
                    'patch' => '21',
                    'preRelease' => 'rc2',
                    'metadata' => '2020-01-01',
                ],
            ],
        ];
    }

    public function casesCreateFromString(): array
    {
        $cases = [];
        foreach ($this->casesTransformation() as $key => $case) {
            $cases[$key] = [
                $case['array'],
                $case['string'],
            ];
        }

        return $cases;
    }

    /**
     * @dataProvider casesCreateFromString
     */
    public function testCreateFromString(array $expected, string $version)
    {
        $instance = VersionNumber::createFromString($version);
        $this->tester->assertSame($expected, $instance->jsonSerialize());
    }

    public function casesToString(): array
    {
        $cases = [];
        foreach ($this->casesTransformation() as $key => $case) {
            $cases[$key] = [
                $case['string'],
                $case['array'],
            ];
        }

        return $cases;
    }

    /**
     * @dataProvider casesToString
     */
    public function testToString(string $expected, array $values): void
    {
        $instance = VersionNumber::__set_state($values);
        $this->tester->assertSame($expected, (string)$instance);
    }

    public function casesFormat(): array
    {
        return [
            'majorMinorZeroPadding' => [
                '703',
                '7.3.21',
                '%{major.d}%{minor.02d}',
            ],
            'majorMinorZeroPadding with leading zero' => [
                '0703',
                '7.3.21',
                '%{major.02d}%{minor.02d}',
            ],
            'all parts' => [
                '070321 preRelease=rc2 metadata=2020-01-01',
                '7.3.21-rc2+2020-01-01',
                '%{major.02d}%{minor.02d}%{patch.02d} preRelease=%{preRelease.s} metadata=%{metadata.s}',
            ],
            '1.x' => [
                '1.x',
                '1.x-dev',
                '%{major.d}.%{minor.s}',
            ],
            '1.0.x' => [
                '1.0.x',
                '1.0.x-dev',
                '%{major.d}.%{minor.d}.%{patch.s}',
            ],
        ];
    }

    /**
     * @dataProvider casesFormat
     */
    public function testFormat(
        string $expected,
        string $version,
        string $format
    ): void {
        $instance = VersionNumber::createFromString($version);
        $this->tester->assertSame($expected, $instance->format($format));
    }

    public function casesFormatConstants(): array
    {
        $version = '1.2.3-alpha1+foo';

        return [
            'MA0DMI0' => [
                '1.2',
                VersionNumber::FORMAT_MA0DMI0,
                $version,
            ],
            'MA0DMI0DP0' => [
                '1.2.3',
                VersionNumber::FORMAT_MA0DMI0DP0,
                $version,
            ],
            'MA0MI2' => [
                '102',
                VersionNumber::FORMAT_MA0MI2,
                $version,
            ],
            'MA0MI2P2' => [
                '10203',
                VersionNumber::FORMAT_MA0MI2P2,
                $version,
            ],
            'MA2' => [
                '01',
                VersionNumber::FORMAT_MA2,
                $version,
            ],
            'MA2MI2' => [
                '0102',
                VersionNumber::FORMAT_MA2MI2,
                $version,
            ],
            'MA2MI2P2' => [
                '010203',
                VersionNumber::FORMAT_MA2MI2P2,
                $version,
            ],
        ];
    }

    /**
     * @dataProvider casesFormatConstants
     */
    public function testFormatConstants(string $expected, string $format, string $version): void
    {
        $instance = VersionNumber::createFromString($version);
        $this->tester->assertSame($expected, $instance->format($format));
    }

    public function casesIsEmpty(): array
    {
        return [
            'empty' => [true, []],
            'major-e' => [true, ['major' => '']],
            'minor-e' => [true, ['minor' => '']],
            'patch-e' => [true, ['patch' => '']],
            'preRelease-e' => [true, ['preRelease' => '']],
            'metadata-e' => [true, ['metadata' => '']],
            'major-0' => [false, ['major' => '0']],
            'minor-0' => [false, ['minor' => '0']],
            'patch-0' => [false, ['patch' => '0']],
            'preRelease-0' => [false, ['preRelease' => '0']],
            'metadata-0' => [false, ['metadata' => '0']],
            'major-1' => [false, ['major' => '1']],
            'minor-1' => [false, ['minor' => '1']],
            'patch-1' => [false, ['patch' => '1']],
            'preRelease-1' => [false, ['preRelease' => '1']],
            'metadata-1' => [false, ['metadata' => '1']],
        ];
    }

    /**
     * @dataProvider casesIsEmpty
     */
    public function testIsEmpty(bool $expected, array $parts): void
    {
        $instance = VersionNumber::__set_state($parts);
        $this->tester->assertSame($expected, $instance->isEmpty());
    }

    public function casesIsValid(): array
    {
        return [
            'empty' => [false, ''],
            '1.2.3-alpha4+foo' => [true, '1.2.3-alpha4+foo'],
            '1.2.3-alpha4' => [true, '1.2.3-alpha4'],
            '1.2.3' => [true, '1.2.3'],
            '1.2' => [true, '1.2'],
            '1' => [true, '1'],
            '1.2-alpha4+foo' => [true, '1.2-alpha4+foo'],
            '1.2-alpha4' => [true, '1.2-alpha4'],
            '1.2+foo' => [true, '1.2+foo'],
            '1-alpha4+foo' => [true, '1-alpha4+foo'],
            '1-alpha4' => [true, '1-alpha4'],
            '1+foo' => [true, '1+foo'],
        ];
    }

    /**
     * @dataProvider casesIsValid
     */
    public function testIsValid(bool $expected, string $version): void
    {
        $this->tester->assertSame($expected, VersionNumber::isValid($version));
    }

    public function casesParsePreRelease(): array
    {
        return [
            'empty' => [null, ''],
            'something' => [null, 'something42'],
            'alpha' => [['type' => 'alpha', 'number' => 1], 'alpha1'],
            'Beta' => [['type' => 'Beta', 'number' => 1], 'Beta1'],
            'rc' => [['type' => 'rc', 'number' => 1], 'rc1'],
            'RC' => [['type' => 'RC', 'number' => 1], 'RC1'],
        ];
    }

    /**
     * @dataProvider casesParsePreRelease
     */
    public function testParsePreRelease(?array $expected, string $preRelease): void
    {
        $this->tester->assertSame($expected, VersionNumber::parsePreRelease($preRelease));
    }

    public function casesBump(): array
    {
        return [
            'major-0' => ['1.2.3-alpha4+foo', '1.2.3-alpha4+foo', 'major', 0],
            'major-1' => ['2.0.0', '1.2.3-alpha4+foo', 'major', 1],
            'major-2' => ['3.0.0', '1.2.3-alpha4+foo', 'major', 2],
            'minor-0' => ['1.2.3-alpha4+foo', '1.2.3-alpha4+foo', 'minor', 0],
            'minor-1' => ['1.3.0', '1.2.3-alpha4+foo', 'minor', 1],
            'patch-0' => ['1.2.3-alpha4+foo', '1.2.3-alpha4+foo', 'patch', 0],
            'patch-1' => ['1.2.4', '1.2.3-alpha4+foo', 'patch', 1],
            'preRelease-0' => ['1.2.3-alpha4+foo', '1.2.3-alpha4+foo', 'preRelease', 0],
            'preRelease-1' => ['1.2.3-alpha5', '1.2.3-alpha4+foo', 'preRelease', 1],
            'preRelease-2' => ['1.2.4-alpha1', '1.2.3+foo', 'preRelease', 1],
            'preRelease-3' => ['1.2.3-alpha4', '1.2.3-alpha1+foo', 'preReleaseVersion', 3],
        ];
    }

    /**
     * @dataProvider casesBump
     */
    public function testBump(string $expected, string $version, $fragment, int $amount): void
    {
        $instance = VersionNumber::createFromString($version);
        $this->tester->assertSame($expected, (string) $instance->bump($fragment, $amount));
    }

    public function testBumpFailFragmentName(): void
    {
        $this->tester->expectThrowable(
            \InvalidArgumentException::class,
            function () {
                $instance = VersionNumber::createFromString('1.2.3-alpha1+foo');
                $instance->bump('none', 42);
            },
        );
    }

    public function testBumpFailNegative(): void
    {
        $this->tester->expectThrowable(
            \OutOfRangeException::class,
            function () {
                $instance = VersionNumber::createFromString('1.2.3-alpha1+foo');
                $instance->bump('major', -42);
            },
        );
    }

    public function testBumpFailMetadata(): void
    {
        $this->tester->expectThrowable(
            \UnexpectedValueException::class,
            function () {
                $instance = VersionNumber::createFromString('1.2.3-alpha1+foo');
                $instance->bump('metadata');
            },
        );
    }

    public function testGetSetRest(): void
    {
        $instance = VersionNumber::createFromString('1.2.3-alpha4+foo');
        $this->tester->assertSame('1', $instance->get('major'));
        $this->tester->assertSame('2', $instance->get('minor'));
        $this->tester->assertSame('3', $instance->get('patch'));
        $this->tester->assertSame('alpha4', $instance->get('pre-release'));
        $this->tester->assertSame('foo', $instance->get('metadata'));

        $instance->reset('metadata');
        $this->tester->assertSame('1.2.3-alpha4', (string) $instance);
        $instance->reset('pre-release');
        $this->tester->assertSame('1.2.3', (string) $instance);
        $instance->reset('patch');
        $this->tester->assertSame('1.2', (string) $instance);
        $instance->reset('minor');
        $this->tester->assertSame('1', (string) $instance);
        $instance->reset();
        $this->tester->assertSame('', (string) $instance);
        $this->tester->assertTrue($instance->isEmpty());

        // @todo Set the "metadata" first when it is empty.
        $instance->set('major', '1');
        $this->tester->assertSame('1', (string) $instance);
        $instance->set('minor', '2');
        $this->tester->assertSame('1.2', (string) $instance);
        $instance->set('patch', '3');
        $this->tester->assertSame('1.2.3', (string) $instance);
        $instance->set('preReleaseVersion', 'beta1');
        $this->tester->assertSame('1.2.3-beta1', (string) $instance);
        $instance->set('metadata', 'baz');
        $this->tester->assertSame('1.2.3-beta1+baz', (string) $instance);
    }

    public function casesDiff(): array
    {
        return [
            'same' => [
                [
                    'spaceship' => 0,
                    'spaceship_with_metadata' => 0,
                    'major' => 0,
                    'minor' => 0,
                    'patch' => 0,
                    'preRelease' => [
                        'type' => 'beta',
                        'number' => 0,
                    ],
                ],
                '1.2.3-beta1+aaa',
                '1.2.3-beta1+aaa',
            ],
            'only metadata' => [
                [
                    'spaceship' => 0,
                    'spaceship_with_metadata' => -1,
                    'major' => 0,
                    'minor' => 0,
                    'patch' => 0,
                    'preRelease' => [
                        'type' => 'beta',
                        'number' => 0,
                    ],
                ],
                '1.2.3-beta1+aaa',
                '1.2.3-beta1+bbb',
            ],
            'basic' => [
                [
                    'spaceship' => -1,
                    'spaceship_with_metadata' => -1,
                    'major' => 1,
                    'minor' => 2,
                    'patch' => 3,
                    'preRelease' => [
                        'type' => 'beta',
                        'number' => 4,
                    ],
                ],
                '1.2.3-beta1+baz',
                '2.4.6-beta5+baz',
            ],
            'without preRelease' => [
                [
                    'spaceship' => -1,
                    'spaceship_with_metadata' => -1,
                    'major' => 1,
                    'minor' => 2,
                    'patch' => 3,
                    'preRelease' => [
                        'type' => 'beta',
                        'number' => 5,
                    ],
                ],
                '1.2.3+baz',
                '2.4.6-beta5+baz',
            ],
            'different preRelease type' => [
                [
                    'spaceship' => -1,
                    'spaceship_with_metadata' => -1,
                    'major' => 1,
                    'minor' => 2,
                    'patch' => 3,
                    'preRelease' => [
                        'type' => 'beta',
                        'number' => 5,
                    ],
                ],
                '1.2.3-rc5+baz',
                '2.4.6-beta5+baz',
            ],
        ];
    }

    /**
     * @dataProvider casesDiff
     */
    public function testDiff(array $expected, string $aVersion, string $bVersion): void
    {
        $a = VersionNumber::createFromString($aVersion);
        $b = VersionNumber::createFromString($bVersion);
        $this->tester->assertSame($expected, $a->diff($b));
    }
}
