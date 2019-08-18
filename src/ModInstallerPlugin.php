<?php

namespace PatrickCurl\ModInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ModInstallerPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new ModInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}
