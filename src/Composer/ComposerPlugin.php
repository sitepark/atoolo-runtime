<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class ComposerPlugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;
    private static bool $activated = false;

    public function activate(Composer $composer, IOInterface $io): void
    {
        self::$activated = true;
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        self::$activated = false;
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        //@unlink($composer->getConfig()->get('vendor-dir').'/autoload_runtime.php');
    }

    public function updateAutoloadFile(): void
    {
    }

    public static function getSubscribedEvents()
    {
        if (!self::$activated) {
            return [];
        }

        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => 'updateAutoloadFile',
        ];
    }
}
