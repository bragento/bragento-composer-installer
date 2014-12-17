<?php
/**
 * Manager.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\Deploy\Manager\Actions;
use Bragento\Magento\Composer\Installer\Deploy\Manager\Entry;
use Bragento\Magento\Composer\Installer\Deploy\Manager\PackageTypes;
use Bragento\Magento\Composer\Installer\Deploy\Strategy\AbstractStrategy;
use Bragento\Magento\Composer\Installer\Deploy\Strategy\Factory;
use Bragento\Magento\Composer\Installer\Exception\NotInitializedException;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Bragento\Magento\Composer\Installer\Util\Gitignore;
use Composer\Composer;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Composer\Script\PackageEvent;
use Composer\Script\ScriptEvents;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Manager
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Manager implements EventSubscriberInterface
{
    /**
     * instance
     *
     * @var Manager
     */
    protected static $instance;

    /**
     * _composer
     *
     * @var Composer
     */
    protected static $composer;

    /**
     * @var IOInterface
     */
    protected static $io;

    /**
     * entries
     *
     * @var Entry[]
     */
    protected $moduleEntries;

    /**
     * entries
     *
     * @var Entry[]
     */
    protected $themeEntries;

    /**
     * _coreEntry
     *
     * @var Entry
     */
    protected $coreEntry;

    /**
     * @var PackageInterface[]
     */
    protected $deployedPackages;

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * private construct for singleton
     */
    private function __construct()
    {
        $this->moduleEntries = array();
        $this->themeEntries = array();
        $this->deployedPackages = array();
    }

    /**
     * init
     *
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     */
    public static function init(Composer $composer, IOInterface $io)
    {
        self::$composer = $composer;
        self::$io = $io;
    }

    /**
     * getInstance
     *
     * @return Manager
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Manager();
        }

        return self::$instance;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_UNINSTALL => 'onPostPackageUninstall'
        );
    }

    /**
     * doDeploy
     *
     * deploy core, then modules, then themes
     *
     * @return void
     */
    public function doDeploy()
    {
        $this->addAllPackages();
        $this->dispatchEvent(Events::PRE_DEPLOY);
        if (null !== $this->coreEntry) {
            $this->coreEntry = $this->deployEntry($this->coreEntry);
        }
        $this->moduleEntries = $this->deployEntriesArray($this->moduleEntries);
        $this->themeEntries = $this->deployEntriesArray($this->themeEntries);
        $this->dispatchEvent(Events::POST_DEPLOY);
    }

    /**
     * addAllPackages
     *
     * @return void
     */
    protected function addAllPackages()
    {
        foreach ($this->getPackages() as $package) {
            if (!in_array($package->getType(), $this->supports())) {
                continue;
            }

            $action = $this->isUndeployedPackage($package)
                ? Actions::INSTALL
                : Actions::UPDATE;

            $this->addEntry(
                $this->getDeployManagerEntry(
                    $package,
                    $action
                )
            );
        }
    }

    /**
     * getPackages
     *
     * @return PackageInterface[]
     */
    protected function getPackages()
    {
        return $this->getComposer()
            ->getRepositoryManager()
            ->getLocalRepository()
            ->getCanonicalPackages();
    }

    /**
     * getComposer
     *
     * @return Composer
     * @throws NotInitializedException
     */
    protected function getComposer()
    {
        if (null === self::$composer) {
            throw new NotInitializedException($this);
        }

        return self::$composer;
    }

    /**
     * supports
     *
     * @return array
     */
    public function supports()
    {
        return array(
            PackageTypes::MAGENTO_CORE,
            PackageTypes::MAGENTO_MODULE,
            PackageTypes::MAGENTO_THEME
        );
    }

    /**
     * isUndeployedPackage
     *
     * @param PackageInterface $package
     *
     * @return bool
     */
    public function isUndeployedPackage(PackageInterface $package)
    {
        return false === $this->getFs()
            ->exists($this->getStateFilePath($package));
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        if (null == $this->fs) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
    }

    /**
     * getStateFilePath
     *
     * @param PackageInterface $package
     *
     * @return string
     */
    public function getStateFilePath(PackageInterface $package)
    {
        return $this->getFs()->joinFileUris(
            $this->getStateDir(),
            str_replace('/', '_', $package->getName())
        );
    }

    /**
     * getStateDir
     *
     * @return string
     */
    public function getStateDir()
    {
        return $this->getFs()->joinFileUris(
            Config::getInstance()->getVendorDir(),
            State::STATE_DIR
        );
    }

    /**
     * addEntry
     *
     * only the first package with type
     * magento-core will be added
     *
     * @param Entry $entry the Entry to add
     *
     * @return void
     */
    public function addEntry(Entry $entry)
    {
        switch ($entry->getDeployStrategy()->getPackage()->getType()) {
            case PackageTypes::MAGENTO_CORE:
                $this->coreEntry = $entry;
                break;

            case PackageTypes::MAGENTO_MODULE:
                $this->moduleEntries[] = $entry;
                break;

            case PackageTypes::MAGENTO_THEME:
                $this->themeEntries[] = $entry;
                break;

            default:
        }
    }

    /**
     * getDeployManagerEntry
     *
     * @param PackageInterface $package package to deploy
     * @param string           $action
     *
     * @return Entry
     */
    protected function getDeployManagerEntry(PackageInterface $package, $action)
    {
        return new Entry(
            $this->getDeployStrategy($package, $action)
        );
    }

    /**
     * getDeployStrategy
     *
     * @param PackageInterface $package
     * @param string           $action
     *
     * @return AbstractStrategy
     */
    protected function getDeployStrategy(PackageInterface $package, $action)
    {
        return Factory::get(
            $package,
            $action,
            $this->getSourceDir($package),
            $this->getTargetDir()
        );
    }

    /**
     * getSourceDir
     *
     * @param PackageInterface $package
     *
     * @return SplFileInfo
     */
    protected function getSourceDir(PackageInterface $package)
    {
        return $this->getFs()->getDir($this->getInstallPath($package));
    }

    /**
     * getInstallPath
     *
     * @param PackageInterface $package
     *
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
        $targetDir = $package->getTargetDir();
        return $this->getPackageBasePath($package)
        . ($targetDir ? '/' . $targetDir : '');
    }

    /**
     * getPackageBasePath
     *
     * @param PackageInterface $package
     *
     * @return string
     */
    protected function getPackageBasePath(PackageInterface $package)
    {
        return $this->getFs()->joinFileUris(
            Config::getInstance()->getVendorDir(),
            $package->getPrettyName()
        );
    }

    /**
     * getTargetDir
     *
     * @return SplFileInfo
     */
    protected function getTargetDir()
    {
        return $this->getDeploymentDir();
    }

    private function dispatchEvent($name)
    {
        $event = new Event(
            $name,
            $this->getComposer(),
            self::$io
        );

        $this->getComposer()->getEventDispatcher()
            ->dispatch($name, $event);
    }

    /**
     * addDeployedPackage
     *
     * @param PackageInterface $package
     *
     * @return void
     */
    protected function addDeployedPackage(PackageInterface $package)
    {
        array_push($this->deployedPackages, $package);
    }

    /**
     * deployEntriesArray
     *
     * @param Entry[] $entries
     *
     * @return Entry[]
     * @throws Exception\UnknownActionException
     */
    protected function deployEntriesArray(array $entries)
    {
        while (count($entries)) {
            /** @var Entry $entry */
            $entry = array_shift($entries);
            $this->deployEntry($entry);
        }

        return $entries;
    }

    /**
     * deployCoreEntry
     *
     * @param Entry $entry
     *
     * @throws Exception\UnknownActionException
     * @return Entry
     */
    protected function deployEntry(Entry &$entry)
    {
        if (null !== $entry) {
            $entry->getDeployStrategy()->doDeploy();
            $this->addDeployedPackage(
                $entry->getDeployStrategy()->getPackage()
            );
            $entry = null;
        }

        return $entry;
    }

    /**
     * getDeploymentDir
     *
     * @return SplFileInfo
     */
    protected function getDeploymentDir()
    {
        return Config::getInstance()->getMagentoRootDir();
    }

    /**
     * onPostPackageUninstall
     *
     * add Entries of uninstalled packages, since they are not
     * in local repository anymore
     *
     * @param PackageEvent $event
     *
     * @return void
     */
    public function onPostPackageUninstall(PackageEvent $event)
    {
        /** @var UninstallOperation $operation */
        $operation = $event->getOperation();
        $this->addEntry(
            $this->getDeployManagerEntry(
                $operation->getPackage(),
                Actions::UNINSTALL
            )
        );
    }

    public function getDeployedPackages()
    {
        return $this->deployedPackages;
    }
}
