<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils;

enum VersionNumberPart: string
{

    case major = 'major';

    case minor = 'minor';

    case patch = 'patch';

    case preRelease = 'preRelease';

    case metadata = 'metadata';
}
