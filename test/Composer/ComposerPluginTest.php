<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Test\Composer;

use Atoolo\Runtime\Composer\ComposerJson;
use Atoolo\Runtime\Composer\ComposerJsonFactory;
use Atoolo\Runtime\Composer\ComposerPlugin;
use Atoolo\Runtime\Composer\RuntimeFile;
use Atoolo\Runtime\Composer\RuntimeFileFactory;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\ScriptEvents;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ComposerPlugin::class)]
class ComposerPluginTest extends TestCase
{
    public function testGetSubscribedEventsWithDeactivated(): void
    {

        $composer = $this->createStub(Composer::class);
        $io = $this->createStub(IOInterface::class);
        $plugin = new ComposerPlugin();
        $plugin->deactivate($composer, $io);

        $this->assertEquals(
            [],
            ComposerPlugin::getSubscribedEvents(),
            'Failed to return empty array'
        );
    }

    public function testGetSubscribedEventsWithActivated(): void
    {

        $composer = $this->createStub(Composer::class);
        $io = $this->createStub(IOInterface::class);
        $plugin = new ComposerPlugin();
        $plugin->activate($composer, $io);

        $method = 'updateRuntime';
        $priority = 1;
        $exprected = [
            ScriptEvents::PRE_AUTOLOAD_DUMP => [$method, $priority],
            ScriptEvents::PRE_INSTALL_CMD => [$method, $priority],
            ScriptEvents::PRE_UPDATE_CMD => [$method, $priority],
        ];

        $this->assertEquals(
            $exprected,
            ComposerPlugin::getSubscribedEvents(),
            'Failed to return empty array'
        );
    }

    public function testUpdateRuntime(): void
    {

        $composer = $this->createStub(Composer::class);
        $io = $this->createStub(IOInterface::class);
        $composerJsonFactory = $this->createStub(ComposerJsonFactory::class);
        $runtimeFileFactory = $this->createStub(RuntimeFileFactory::class);
        $plugin = new ComposerPlugin(
            $composerJsonFactory,
            $runtimeFileFactory
        );

        $runtimeFile = $this->createMock(RuntimeFile::class);
        $runtimeFile->expects($this->once())
            ->method('updateRuntimeFile');
        $runtimeFileFactory->method('create')
            ->willReturn($runtimeFile);
        $composerJson = $this->createMock(ComposerJson::class);
        $composerJson->expects($this->once())
            ->method('addAutoloadFile');
        $composerJsonFactory->method('create')
            ->willReturn($composerJson);

        $plugin->activate($composer, $io);
        $plugin->updateRuntime();
    }

    public function testUninstall(): void
    {

        $composer = $this->createStub(Composer::class);
        $io = $this->createStub(IOInterface::class);
        $composerJsonFactory = $this->createStub(ComposerJsonFactory::class);
        $runtimeFileFactory = $this->createStub(RuntimeFileFactory::class);
        $plugin = new ComposerPlugin(
            $composerJsonFactory,
            $runtimeFileFactory
        );

        $runtimeFile = $this->createMock(RuntimeFile::class);
        $runtimeFile->expects($this->once())
            ->method('removeRuntimeFile');
        $runtimeFileFactory->method('create')
            ->willReturn($runtimeFile);
        $composerJson = $this->createMock(ComposerJson::class);
        $composerJson->expects($this->once())
            ->method('removeAutoloadFile');
        $composerJsonFactory->method('create')
            ->willReturn($composerJson);

        $plugin->activate($composer, $io);
        $plugin->uninstall($composer, $io);
    }
}
