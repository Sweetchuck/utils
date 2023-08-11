<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

class Uri
{

    /**
     * @see \parse_url()
     * @see \http_build_url()
     */
    public static function build(array $parts, string $passwordFormat = 'raw'): string
    {
        $uri = '';
        if (isset($parts['scheme']) && $parts['scheme'] !== '') {
            $uri .= $parts['scheme'] . '://';
        }

        if (isset($parts['user']) && $parts['user'] !== '') {
            $uri .= urlencode($parts['user']);
            if (isset($parts['pass']) && $parts['pass'] !== '') {
                switch ($passwordFormat) {
                    case 'placeholder':
                        $uri .= ':****';
                        break;
                    case 'hidden':
                        break;
                    default:
                        $uri .= ':' . urlencode($parts['pass']);
                        break;
                }
            }

            $uri .= '@';
        }

        if (isset($parts['host']) && $parts['host'] !== '') {
            $uri .= $parts['host'];
            if (!empty($parts['port'])) {
                $uri .= ':' . $parts['port'];
            }
        }

        if (isset($parts['path']) && $parts['path'] !== '') {
            if (substr($parts['path'], 0, 1) !== '/') {
                $uri .= '/';
            }

            $uri .= $parts['path'];
        }

        if (!empty($parts['query'])) {
            $uri .= '?' . (is_string($parts['query']) ? $parts['query'] : http_build_query($parts['query']));
        }

        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $uri .= '#' . $parts['fragment'];
        }

        return $uri;
    }
}
