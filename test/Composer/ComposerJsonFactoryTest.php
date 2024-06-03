<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Test\Composer;

use Atoolo\Runtime\Composer\ComposerJson;
use Atoolo\Runtime\Composer\ComposerJsonFactory;
use Composer\Composer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ComposerJsonFactory::class)]
class ComposerJsonFactoryTest extends TestCase
{
    private string $resourceDir = __DIR__
    . '/../resources/Composer/ComposerJsonFactory';

    public function testCreate(): void
    {
        $factory = new ComposerJsonFactory();
        $composer = $this->createStub(Composer::class);
        $composerJson = $factory->create(
            $composer,
            $this->resourceDir . '/valid-composer.json'
        );
        self::assertInstanceOf(ComposerJson::class, $composerJson);
    }
}
