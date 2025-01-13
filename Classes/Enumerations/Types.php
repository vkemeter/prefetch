<?php

declare(strict_types=1);

namespace Supseven\Prefetch\Enumerations;

use TYPO3\CMS\Core\Type\Enumeration;

final class Types extends Enumeration
{
    public const __default = self::PREFETCH;
    public const PREFETCH = 0;
    public const PRERENDER = 1;
}
