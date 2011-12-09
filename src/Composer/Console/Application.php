<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Finder\Finder;
use Composer\Command;
use Composer\Composer;
use Composer\Config;
use Composer\Installer;
use Composer\Downloader;
use Composer\Repository;
use Composer\Package;
use Composer\Json\JsonFile;

/**
 * The console application that handles the commands
 *
 * @author Ryan Weaver <ryan@knplabs.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Application extends BaseApplication
{
    protected $composer;

    public function __construct()
    {
        parent::__construct('Composer', Composer::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $styles['highlight'] = new OutputFormatterStyle('red');
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();

        return parent::doRun($input, $output);
    }

    /**
     * @return Composer
     */
    public function getComposer()
    {
        if (null === $this->composer) {
            $this->composer = self::bootstrapComposer();
        }

        return $this->composer;
    }


    /**
     * @param null|string $composerFile
     * @return \Composer\Config
     */
    static private function getConfigurationFile($composerFile = null)
    {
        // load Composer configuration
        if (null === $composerFile) {
            $composerFile = getenv('COMPOSER') ? : 'composer.json';
        }

        $file = new JsonFile($composerFile);
        if (!$file->exists()) {
            if ($composerFile === 'composer.json') {
                echo 'Composer could not find a composer.json file in ' . getcwd() . PHP_EOL;
            } else {
                echo 'Composer could not find the config file: ' . $composerFile . PHP_EOL;
            }
            echo 'To initialize a project, please create a composer.json file as described on the http://packagist.org/ "Getting Started" section' . PHP_EOL;
            exit(1);
        }

        return new Config($file->read());
    }


    /**
     * Bootstraps a Composer instance
     *
     * @param string|null $composerFile
     * @return Composer
     */
    public static function bootstrapComposer($composerFile = null)
    {
        // Configuration defaults
        $config = new Config(array(
            'config' => array(
                'vendor-dir' => 'vendor',
            ),
        ));

        $packageConfig = self::getConfigurationFile($composerFile);
        $config->merge($packageConfig);

        $vendorDir = $config->get('config/vendor-dir');

        if (!$config->has('config/bin-dir')) {
            $config->set('config/bin-dir', $vendorDir.'/bin');
        }
        $binDir = $config->get('config/bin-dir');

        // initialize repository manager
        $rm = new Repository\RepositoryManager();
        $rm->setLocalRepository(new Repository\FilesystemRepository(new JsonFile($vendorDir.'/.composer/installed.json')));
        $rm->setRepositoryClass('composer', 'Composer\Repository\ComposerRepository');
        $rm->setRepositoryClass('vcs', 'Composer\Repository\VcsRepository');
        $rm->setRepositoryClass('pear', 'Composer\Repository\PearRepository');
        $rm->setRepositoryClass('package', 'Composer\Repository\PackageRepository');

        // initialize download manager
        $dm = new Downloader\DownloadManager();
        $dm->setDownloader('git',  new Downloader\GitDownloader());
        $dm->setDownloader('svn',  new Downloader\SvnDownloader());
        $dm->setDownloader('hg', new Downloader\HgDownloader());
        $dm->setDownloader('pear', new Downloader\PearDownloader());
        $dm->setDownloader('zip',  new Downloader\ZipDownloader());

        // initialize installation manager
        $im = new Installer\InstallationManager($vendorDir);
        $im->addInstaller(new Installer\LibraryInstaller($vendorDir, $binDir, $dm, $rm->getLocalRepository(), null));
        $im->addInstaller(new Installer\InstallerInstaller($vendorDir, $binDir, $dm, $rm->getLocalRepository(), $im));

        // load package
        $loader  = new Package\Loader\ArrayLoader($rm);
        $package = $loader->load($config->getData());

        // load default repository unless it's explicitly disabled
        if (!$config->has('repositories/packagist') || $config->get('repositories/packagist') !== false) {
            $rm->addRepository(new Repository\ComposerRepository(array('url' => 'http://packagist.org')));
        }

        // init locker
        $lockFile = substr($composerFile, -5) === '.json' ? substr($composerFile, 0, -4).'lock' : $composerFile . '.lock';
        $locker = new Package\Locker(new JsonFile($lockFile), $rm);

        // initialize composer
        $composer = new Composer($config);
        $composer->setPackage($package);
        $composer->setLocker($locker);
        $composer->setRepositoryManager($rm);
        $composer->setDownloadManager($dm);
        $composer->setInstallationManager($im);

        return $composer;
    }

    /**
     * Initializes all the composer commands
     */
    protected function registerCommands()
    {
        $this->add(new Command\AboutCommand());
        $this->add(new Command\InstallCommand());
        $this->add(new Command\SetupCommand());
        $this->add(new Command\UpdateCommand());
        $this->add(new Command\DebugPackagesCommand());
        $this->add(new Command\SearchCommand());

        if ('phar:' === substr(__FILE__, 0, 5)) {
            $this->add(new Command\SelfUpdateCommand());
        }
    }
}
