<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Executor;

use Symfony\Component\Dotenv\Dotenv;

/**
 * @codeCoverageIgnore
 */
class Platform
{
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
    }

    public function umask(int $umask): int
    {
        return umask($umask);
    }

    public function setIni(
        string $name,
        bool|float|int|string $value,
    ): false|string {
        return ini_set($name, $value);
    }

    public function putEnv(
        string $name,
        string $value,
    ): bool {
        $this->dotenv->populate([$name => $value]);
        return true;
    }
}
