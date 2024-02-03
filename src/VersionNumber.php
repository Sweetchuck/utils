<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

/**
 * @phpstan-import-type VersionNumberParts from \Sweetchuck\Utils\Phpstan
 *
 * @link https://semver.org/
 *
 * @property-read string $formatMA0DMI0
 * @property-read string $formatMA0DMI0DP0
 * @property-read string $formatMA0DMI0DP0R
 * @property-read string $formatMA0MI2
 * @property-read string $formatMA0MI2P2
 * @property-read string $formatMA2
 * @property-read string $formatMA2MI2
 * @property-read string $formatMA2MI2P2
 */
class VersionNumber implements \JsonSerializable
{
    /**
     * WARNING: This allows non-numeric values as well.
     * For example: "a.b" or "1.x".
     *
     * @todo Strict format checking.
     * @todo Major should be numeric only (0|[1-9][0-9]*).
     * @todo Minor should be numeric or "x" if patch is omitted.
     * @todo Patch should be numeric or "x".
     * @todo PreRelease format checking.
     */
    const VERSION_PARSER_REGEX = <<<'REGEXP'
/^
  (?P<major>[^.+-]+)
  (
    \.(?P<minor>[^.+-]+)
      (\.(?P<patch>[^+-]+))?
  )?
  (-(?P<preRelease>[^+]+))?
  (\+(?P<metadata>.+))?
$/ux
REGEXP;

    const PRE_RELEASE_PARSER_REGEX = '/^(?P<type>(alpha|beta|rc)\.?)(?P<number>\d+)$/ui';

    const FORMAT_PRE_RELEASE = '%{type.s}%{number.d}';

    /**
     * Format "1.2.3-beta4+foo" to "1.2".
     */
    const FORMAT_MA0DMI0 = '%{major.d}.%{minor.s}';

    /**
     * Format "1.2.3-beta4+foo" to "1.2.3".
     */
    const FORMAT_MA0DMI0DP0 = '%{major.d}.%{minor.s}.%{patch.s}';

    /**
     * Format "1.2.3-beta4+foo" to "1.2.3-beta4".
     */
    const FORMAT_MA0DMI0DP0R = '%{major.d}.%{minor.s}.%{patch.s}-%{preRelease.s}';

    /**
     * Format "1.2.3-beta4+foo" to "102".
     */
    const FORMAT_MA0MI2 = '%{major.d}%{minor.02d}';

    /**
     * Format "1.2.3-beta4+foo" to "10203".
     */
    const FORMAT_MA0MI2P2 = '%{major.d}%{minor.02d}%{patch.02d}';

    /**
     * Format "1.2.3-beta4+foo" to "01".
     */
    const FORMAT_MA2 = '%{major.02d}';

    /**
     * Format "1.2.3-beta4+foo" to "0102".
     */
    const FORMAT_MA2MI2 = '%{major.02d}%{minor.02d}';

    /**
     * Format "1.2.3-beta4+foo" to "010203".
     */
    const FORMAT_MA2MI2P2 = '%{major.02d}%{minor.02d}%{patch.02d}';

    /**
     * @var string[]
     */
    protected static array $propertyMapping = [
        'major' => 'major',
        'minor' => 'minor',
        'patch' => 'patch',
        'pre-release' => 'preRelease',
        'preRelease' => 'preRelease',
        'preReleaseVersion' => 'preRelease',
        'metadata' => 'metadata',
    ];

    /**
     * @var string[]
     */
    protected static array $defaultValues = [
        'major' => '',
        'minor' => '',
        'patch' => '',
        'preRelease' => '',
        'metadata' => '',
    ];

    /**
     * @return string[]
     */
    public static function getFragmentNames(): array
    {
        return array_keys(static::$defaultValues);
    }

    public string $major = '';

    public string $minor = '';

    public string $patch = '';

    public string $preRelease = '';

    public string $metadata = '';

    /**
     * @phpstan-param VersionNumberParts $parts
     *
     * @return static
     */
    public static function __set_state(array $parts)
    {
        // @phpstan-ignore-next-line new.static
        $instance = new static();
        $instance->major = (string) ($parts['major'] ?? $parts[0] ?? '');
        $instance->minor = (string) ($parts['minor'] ?? $parts[1] ?? '');
        $instance->patch = (string) ($parts['patch'] ?? $parts[2] ?? '');
        $instance->preRelease = (string) ($parts['preRelease'] ?? $parts[3] ?? '');
        $instance->metadata = (string) ($parts['metadata'] ?? $parts[4] ?? '');

        return $instance;
    }

    public static function trimPrefix(string $version): string
    {
        return preg_replace('/^v(\d+\.)/u', '$1', $version);
    }

    public static function createFromString(string $version, bool $trimPrefix = true): static
    {
        return static::__set_state(static::explode($version, $trimPrefix));
    }

    /**
     * @phpstan-return VersionNumberParts
     */
    public static function explode(string $version, bool $trimPrefix = true): array
    {
        if ($trimPrefix) {
            $version = static::trimPrefix($version);
        }

        $matches = [];
        preg_match(static::VERSION_PARSER_REGEX, $version, $matches);

        return array_filter(
            $matches,
            function ($key) {
                return !is_numeric($key);
            },
            \ARRAY_FILTER_USE_KEY,
        );
    }

    public static function isValid(string $version, bool $trimPrefix = true): bool
    {
        if ($trimPrefix) {
            $version = static::trimPrefix($version);
        }

        return preg_match(static::VERSION_PARSER_REGEX, $version) === 1;
    }

    /**
     * @return null|mixed[]
     */
    public static function parsePreRelease(string $preRelease): ?array
    {
        $matches = [];

        return preg_match(static::PRE_RELEASE_PARSER_REGEX, $preRelease, $matches) ?
            [
                'type' => $matches['type'],
                'number' => (int) $matches['number'],
            ]
            : null;
    }

    public static function assertFragmentName(string $name): void
    {
        if (isset(static::$propertyMapping[$name])) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'invalid fragment name: "%s" allowed values: %s',
            $name,
            implode(', ', array_keys(static::$propertyMapping)),
        ));
    }

    protected StringUtils $stringUtils;

    public function __construct(?StringUtils $stringUtils = null)
    {
        $this->stringUtils = $stringUtils ?: new StringUtils();
    }

    public function __toString()
    {
        $version = $this->major;
        if (strlen($this->minor)) {
            $version .= '.' . $this->minor;
            if (strlen($this->patch)) {
                $version .= '.' . $this->patch;
            }
        }

        if (strlen($this->preRelease)) {
            $version .= "-{$this->preRelease}";
        }

        if (strlen($this->metadata)) {
            $version .= "+{$this->metadata}";
        }

        return $version;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $constantName = $this->magicFormatPropertyNameToConstantName($name);

        return $constantName && defined($constantName);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $constantName = $this->magicFormatPropertyNameToConstantName($name);
        if ($constantName === null || !defined($constantName)) {
            $trace = debug_backtrace();
            trigger_error(sprintf(
                'Undefined property via __get(): %s in %s on line %s',
                $name,
                $trace[0]['file'] ?? 'unknown://nowhere.txt',
                $trace[0]['line'] ?? '0',
            ));

            return null;
        }

        return $this->format(constant($constantName));
    }

    protected function magicFormatPropertyNameToConstantName(string $propertyName): ?string
    {
        $constantName = preg_replace('/^format/', 'FORMAT_', $propertyName);

        return str_starts_with($constantName, 'FORMAT_') ?
            static::class . "::$constantName"
            : null;
    }

    /**
     * @phpstan-return VersionNumberParts
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach (array_unique(static::$propertyMapping) as $name) {
            $value = $this->get($name);
            if ($value !== '') {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    public function get(string $name): string
    {
        static::assertFragmentName($name);

        return $this->{static::$propertyMapping[$name]};
    }

    public function set(string $name, string $value): static
    {
        static::assertFragmentName($name);
        $this->{static::$propertyMapping[$name]} = $value;

        return $this;
    }

    public function reset(string $fragment = 'major'): static
    {
        static::assertFragmentName($fragment);
        switch ($fragment) {
            case 'major':
                $this->major = '';
                // no break
            case 'minor':
                $this->minor = '';
                // no break
            case 'patch':
                $this->patch = '';
                // no break
            case 'pre-release':
            case 'preRelease':
            case 'preReleaseVersion':
                $this->preRelease = '';
                // no break
            default:
                $this->metadata = '';
                break;
        }

        return $this;
    }

    public function isEmpty(): bool
    {
        return (
            $this->major === ''
            && $this->minor === ''
            && $this->patch === ''
            && $this->preRelease === ''
            && $this->metadata === ''
        );
    }

    /**
     * @phpstan-param array<string, mixed> $args
     */
    public function format(string $format, array $args = []): string
    {
        return $this->stringUtils->vsprintf(
            $format,
            $this->jsonSerialize() + static::$defaultValues + $args,
        );
    }

    public function bump(string $fragment, int $amount = 1): static
    {
        static::assertFragmentName($fragment);
        if ($amount === 0) {
            return $this;
        }

        if ($amount < 0) {
            throw new \OutOfRangeException('$amount can not be negative');
        }

        $this
            ->bumpReset($fragment)
            ->bumpIncrease($fragment, $amount);

        return $this;
    }

    protected function bumpReset(string $fragment): static
    {
        switch ($fragment) {
            case 'major':
                $this->minor = '0';
            // no break
            case 'minor':
                $this->patch = '0';
            // no break
            case 'patch':
                $this->preRelease = '';
            // no break
            case 'pre-release':
            case 'preRelease':
            case 'preReleaseVersion':
                $this->metadata = '';
                break;

            default:
                throw new \UnexpectedValueException("@todo Not implemented yet; fragment: $fragment", 1);
        }

        return $this;
    }

    protected function bumpIncrease(string $fragment, int $amount): static
    {
        switch ($fragment) {
            case 'major':
                $this->major = (string) (intval($this->major) + $amount);
                break;

            case 'minor':
                $this->minor = (string) (intval($this->minor) + $amount);
                break;

            case 'patch':
                $this->patch = (string) (intval($this->patch) + $amount);
                break;

            case 'pre-release':
            case 'preRelease':
            case 'preReleaseVersion':
                if (!$this->preRelease) {
                    $this->patch = (string) (intval($this->patch) + 1);
                    $this->preRelease = 'alpha1';

                    break;
                }

                $parts = static::parsePreRelease($this->preRelease);
                if ($parts) {
                    $this->preRelease = $this->stringUtils->vsprintf(
                        static::FORMAT_PRE_RELEASE,
                        [
                            'type' => $parts['type'],
                            'number' => $parts['number'] + $amount,
                        ],
                    );
                }

                break;
        }

        return $this;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    public function diff(self $other): array
    {
        $diff = [
            'spaceship' => version_compare(
                $this->format(static::FORMAT_MA0DMI0DP0R),
                $other->format(static::FORMAT_MA0DMI0DP0R),
            ),
            'spaceship_with_metadata' => version_compare((string) $this, (string) $other),
        ];

        $a = $this->jsonSerialize();
        $b = $other->jsonSerialize();

        foreach (['major', 'minor', 'patch'] as $name) {
            if (!isset($a[$name]) || !is_numeric($a[$name])
                || !isset($b[$name]) || !is_numeric($b[$name])
            ) {
                continue;
            }

            $diff[$name] = $b[$name] - $a[$name];
        }

        $aPre = static::parsePreRelease($this->preRelease);
        $bPre = static::parsePreRelease($other->preRelease);

        if (!$aPre && $bPre) {
            $diff['preRelease'] = $bPre;
        } elseif ($aPre && $bPre) {
            if ($aPre['type'] === $bPre['type']) {
                $diff['preRelease'] = [
                    'type' => $aPre['type'],
                    'number' => $bPre['number'] - $aPre['number'],
                ];
            } elseif (version_compare($aPre['type'], $bPre['type']) === 1) {
                $diff['preRelease'] = $bPre;
            }
        }

        return $diff;
    }
}
