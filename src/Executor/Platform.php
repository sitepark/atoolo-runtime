<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Executor;

/**
 * @codeCoverageIgnore
 */
class Platform
{
    public function umask(int $umask): int
    {
        return umask($umask);
    }

    public function setIni(
        string $name,
        bool|float|int|string $value
    ): false|string {
        return ini_set($name, $value);
    }
}
