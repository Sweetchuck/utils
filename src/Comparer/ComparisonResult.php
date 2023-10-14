<?php

declare(strict_types = 1);

namespace Sweetchuck\Utils\Comparer;

enum ComparisonResult: int
{
    case LeftIsSmaller = 1;

    case Equal = 0;

    case RightIsSmaller = -1;
}
