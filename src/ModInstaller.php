<?php

namespace PatrickCurl\ModInstaller;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\Finder;

class ModInstaller extends LibraryInstaller
{
    protected $defaultRoot;

    protected $basePath;

    protected $packageConfig;

    protected $moduleConfig;

    protected $extra;

    protected $composer;

    protected $vendorDir;

    protected $binDir;

    protected $downloadManager;

    protected $io;

    protected $type;

    protected $filesystem;

    protected $binCompat;

    protected $binaryInstaller;

    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null, BinaryInstaller $binaryInstaller = null)
    {
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
        // $autoload   = $baseDir . '/vendor/autoload.php';
        // $baseDir    = dirname($this->composer->getConfig()->get('vendor-dir'));
        // $helpers    = $baseDir . '/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php';
        // $basedir = realpath(dirname($this->composer->getConfig()->get('vendor-dir')));
        // require_once($basedir .'/vendor/autoload.php');
        // $this->laravel   = require_once $basedir . '/bootstrap/app.php';
        $this->defaultRoot   = 'Modules';
        $this->basePath      = realpath(dirname($this->composer->getConfig()->get('vendor-dir')));
        require_once($this->basePath .'/vendor/autoload.php');
        require_once($this->basePath . '/vendor/laravel/framework/src/Illuminate/Support/helpers.php');
        require_once($this->basePath.'/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php');
        require_once($this->basePath . '/bootstrap/app.php');
        $this->moduleConfig  = $this->getConfig();
        $this->packageConfig = $this->moduleConfig['packages'] ?? [];
    }

    /**
     * Get the fully-qualified install path
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $modName         = $this->getModuleName($package);
        $baseInstallPath = $this->getBaseInstallationPath();
        $moduleName      = $this->getModuleName($package);

        if (empty($baseInstallPath) || empty($moduleName)) {
            return $this->defaultInstallPath($package);
        }

        return $this->getBaseInstallationPath() . '/' . $this->getModuleName($package);
    }

    public function defaultInstallPath(PackageInterface $package)
    {
        $this->initializeVendorDir();

        $basePath  = ($this->vendorDir ? $this->vendorDir.'/' : '') . $package->getPrettyName();
        $targetDir = $package->getTargetDir();

        return $basePath . ($targetDir ? '/'.$targetDir : '');
    }

    /**
     * Get the base path that the module should be installed into.
     * Defaults to Modules/ and can be overridden in the module's composer.json.
     * @return string
     */
    protected function getBaseInstallationPath()
    {
        $config  = $this->moduleConfig;

        if ($config && !empty($config['path'])) {
            return $config['path'];
        }

        if (!$this->composer || !$this->composer->getPackage()) {
            return $this->defaultRoot;
        }

        $extra   = $this->extra;



        if (!$extra || empty($extra['module-dir'])) {
            return $this->defaultRoot;
        }

        return $extra['module-dir'];
    }

    /**
     * Get the module name, i.e. "joshbrw/something-module" will be transformed into "Something"
     * @param PackageInterface $package
     * @throws \Exception
     * @return string
     */
    protected function getModuleName(PackageInterface $package)
    {
        $name  = $package->getPrettyName();

        if (!empty($this->packageConfig)) {
            $packageConf = collect($this->packageConfig)->where('package', $name)->first();

            if (!empty($packageConf['name'])) {
                return $packageConf['name'];
            }
        }

        $extra = $package->getExtra();

        if ($extra && !empty($extra['module']['name'])) {
            return $extra['module']['name'];
        }

        // $split = explode('/', $name);

        // if (count($split) !== 2) {
        //     // throw new \Exception($this->usage());
        // }

        // $splitNameToUse = explode('-', $split[1]);

        // if (count($splitNameToUse) < 2) {
        //     // throw new \Exception($this->usage());
        // }

        // if (array_pop($splitNameToUse) !== 'module') {
        //     // throw new \Exception($this->usage());
        // }

        return null;
        //return implode('', array_map('ucfirst', $splitNameToUse));
    }

    private function getConfig()
    {
        $modules = include($this->basePath . '/config/modules.php');

        return $modules;
    }

    /**
     * Get the usage instructions
     * @return string
     */
    protected function usage()
    {
        return "Ensure your package's name is in the format <vendor>/<name>-<module>";
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'laravel-module' === $packageType;
    }
}
