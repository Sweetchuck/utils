<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

/**
 * @link https://semver.org/
 */
class VersionNumber implements \JsonSerializable
{
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

    protected static array $propertyMapping = [
        'major' => 'major',
        'minor' => 'minor',
        'patch' => 'patch',
        'pre-release' => 'preRelease',
        'preRelease' => 'preRelease',
        'preReleaseVersion' => 'preRelease',
        'metadata' => 'metadata',
    ];

    protected static array $defaultValues = [
        'major' => '',
        'minor' => '',
        'patch' => '',
        'preRelease' => '',
        'metadata' => '',
    ];

    public string $major = '';

    public string $minor = '';

    public string $patch = '';

    public string $preRelease = '';

    public string $metadata = '';

    /**
     * @return static
     */
    public static function __set_state(array $parts)
    {
        $instance = new static();
        $instance->major = (string) ($parts['major'] ?? $parts[0] ?? '');
        $instance->minor = (string) ($parts['minor'] ?? $parts[1] ?? '');
        $instance->patch = (string) ($parts['patch'] ?? $parts[2] ?? '');
        $instance->preRelease = (string) ($parts['preRelease'] ?? $parts[3] ?? '');
        $instance->metadata = (string) ($parts['metadata'] ?? $parts[4] ?? '');

        return $instance;
    }

    /**
     * @return static
     */
    public static function createFromString(string $version)
    {
        return static::__set_state(static::explode($version));
    }

    public static function explode(string $version): array
    {
        $matches = [];
        preg_match(static::VERSION_PARSER_REGEX, $version, $matches);

        return array_filter(
            $matches,
            function ($key) {
                return !is_numeric($key);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function isValid(string $version): bool
    {
        return preg_match(static::VERSION_PARSER_REGEX, $version) === 1;
    }

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
     * {@inheritdoc}
     */
    public function jsonSerialize()
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
        $this->assertFragmentName($name);

        return $this->{static::$propertyMapping[$name]};
    }

    /**
     * @return $this
     */
    public function set(string $name, string $value)
    {
        $this->assertFragmentName($name);
        $this->{static::$propertyMapping[$name]} = $value;

        return $this;
    }

    /**
     * @return $this
     *
     * @noinspection PhpMissingBreakStatementInspection
     */
    public function reset(string $fragment = 'major')
    {
        $this->assertFragmentName($fragment);
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

    public function format(string $format, array $args = []): string
    {
        return StringFormat::vsprintf(
            $format,
            $this->jsonSerialize() + static::$defaultValues + $args,
        );
    }

    /**
     * @return $this
     */
    public function bump(string $fragment, int $amount = 1)
    {
        $this->assertFragmentName($fragment);
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

    /**
     * @return $this
     *
     * @noinspection PhpMissingBreakStatementInspection
     */
    protected function bumpReset(string $fragment)
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
                throw new \UnexpectedValueException('@todo Not implemented yet', 1);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function bumpIncrease(string $fragment, int $amount)
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
                    $this->preRelease = StringFormat::vsprintf(
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
     * @param static $other
     */
    public function diff($other): array
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

    protected function assertFragmentName(string $name)
    {
        if (isset(static::$propertyMapping[$name])) {
            return $this;
        }

        throw new \InvalidArgumentException(sprintf(
            'invalid fragment name: "%s" allowed values: %s',
            $name,
            implode(', ', array_keys(static::$propertyMapping)),
        ));
    }
}
