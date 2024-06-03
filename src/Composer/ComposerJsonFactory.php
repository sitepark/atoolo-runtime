<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Composer;

use Composer\Composer;

class ComposerJsonFactory
{
    public function create(
        Composer $composer,
        string $composerJsonFile
    ): ComposerJson {
        $composerJson = new ComposerJson($composer);
        $composerJson->load($composerJsonFile);
        return $composerJson;
    }
}
