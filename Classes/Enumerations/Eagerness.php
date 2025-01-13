<?php

declare(strict_types=1);

namespace Supseven\Prefetch\Enumerations;

use TYPO3\CMS\Core\Type\Enumeration;

final class Eagerness extends Enumeration
{
    public const __default = self::MODERATE;
    public const IMMEDIATE = 0;
    public const EAGER = 1;
    public const MODERATE = 2;
    public const CONSERVATIVE = 3;
}
