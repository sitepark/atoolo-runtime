<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Executor;

use RuntimeException;

class IniSetter implements RuntimeExecutor
{
    /**
     * @var array{}|array{
     *   string: array{
     *     value: bool|float|int|string,
     *     package: string
     *   }
     * }
     */
    private $alreadySet = [];

    /**
     * @param RuntimeOptions $options
     */
    public function execute(string $projectDir, array $options): void
    {
        foreach ($options as $package => $packageOptions) {
            foreach ($packageOptions['ini']['set'] ?? [] as $key => $value) {
                if ($value === null) {
                    continue;
                }
                if (is_scalar($value) === false) {
                    throw new RuntimeException(
                        "[atoolo.runtime.init.set]: "
                        . "Value for $key in package $package must be scalar"
                    );
                }
                $this->initSet($package, $key, $value);
            }
        }
    }

    private function initSet(
        string $package,
        string $key,
        bool|float|int|string|null $value
    ): void {
        if (isset($this->alreadySet[$key])) {
            $existsValue = $this->alreadySet[$key]['value'];
            if ($existsValue === $value) {
                return;
            }
            $package  = $this->alreadySet[$key]['package'];
            throw new RuntimeException(
                "[atoolo.runtime.init.set]: "
                . "$key is already set to '$existsValue', package: $package"
            );
        }

        if (ini_set($key, $value) === false) {
            throw new RuntimeException(
                "[atoolo.runtime.init.set]: "
                . "Failed to set $key to $value for, package: $package"
            );
        }

        $this->alreadySet[$key] = [
            'value' => $value,
            'package' => $package
        ];
    }
}
