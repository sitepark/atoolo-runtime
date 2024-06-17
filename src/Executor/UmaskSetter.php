<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Executor;

use RuntimeException;

class UmaskSetter implements RuntimeExecutor
{
    public function __construct(
        private readonly Platform $platform = new Platform()
    ) {
    }

    public function execute(string $projectDir, array $options): void
    {
        $alreadySet = null;
        foreach ($options as $package => $packageOptions) {
            if (!isset($packageOptions['umask'])) {
                continue;
            }
            $value = $packageOptions['umask'];
            if ($alreadySet !== null) {
                $existsValue = $alreadySet['value'];
                if ($existsValue === $value) {
                    continue;
                }
                $package  = $alreadySet['package'];
                throw new RuntimeException(
                    "[atoolo.runtime.umask]: '
                    . 'umask is already set to $existsValue '
                    . ' for package $package"
                );
            }

            if (!is_numeric($value)) {
                throw new RuntimeException(
                    "[atoolo.runtime.umask]: '
                    . 'umask must be an integer: "
                    . $value
                );
            }

            $this->platform->umask((int)$value);
            $alreadySet = [
                'value' => $value,
                'package' => $package
            ];
        }
    }
}
