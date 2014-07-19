<?php
/**
 * AbstractStrategy.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;

use Bragento\Magento\Composer\Installer\Deploy\Exception\UnknownActionException;
use Bragento\Magento\Composer\Installer\Deploy\Manager\Actions;
use Bragento\Magento\Composer\Installer\Deploy\Operation\Deploy;
use Bragento\Magento\Composer\Installer\Mapping;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Script\PackageEvent;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class AbstractStrategy
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractStrategy
{
    const EVENT_TIMING_PRE = 'pre';
    const EVENT_TIMING_POST = 'post';

    const MAPPINGS_DIR = 'mage-deploy-mappings';

    /**
     * _package
     *
     * @var PackageInterface
     */
    private $_package;

    /**
     * _sourceDir
     *
     * @var SplFileInfo
     */
    private $_sourceDir;

    /**
     * _destDir
     *
     * @var SplFileInfo
     */
    private $_destDir;

    /**
     * _action
     *
     * @var string
     */
    private $_action;

    /**
     * _eventDispatcher
     *
     * @var EventDispatcher
     */
    private $_eventDispatcher;

    /**
     * _composer
     *
     * @var Composer
     */
    private $_composer;

    /**
     * _io
     *
     * @var IOInterface
     */
    private $_io;

    /**
     * _mapping
     *
     * @var Mapping\AbstractMapping
     */
    private $_mapping;

    /**
     * _deployedFilesMapping
     *
     * @var array
     */
    private $_deployedDelegatesMapping;

    /**
     * _fs
     *
     * @var Filesystem
     */
    private $_fs;

    /**
     * construct Deploy Strategy
     *
     * @param PackageInterface         $package   Package to deploy
     * @param SplFileInfo              $sourceDir Source Directory
     * @param SplFileInfo              $destDir   Destination Directory
     * @param string                   $action    Deploy Action
     * @param \Composer\Composer       $composer  composer instance
     * @param \Composer\IO\IOInterface $io        IO Interface
     */
    function __construct(
        PackageInterface $package,
        SplFileInfo $sourceDir,
        SplFileInfo $destDir,
        $action,
        Composer $composer,
        IOInterface $io
    ) {
        $this->_package = $package;
        $this->_sourceDir = $sourceDir;
        $this->_destDir = $destDir;
        $this->_action = $action;
        $this->_eventDispatcher = $composer->getEventDispatcher();
        $this->_composer = $composer;
        $this->_io = $io;
        $this->_fs = new Filesystem();
    }

    /**
     * doDeploy
     *
     * @throws UnknownActionException
     * @return void
     */
    public function doDeploy()
    {
        $this->_dispatchActionEvent(self::EVENT_TIMING_PRE);

        switch ($this->getAction()) {
            case Actions::INSTALL:
                $this->makeInstall();
                break;

            case Actions::UPDATE:
                $this->makeUpdate();
                break;

            case Actions::UNINSTALL;
                $this->makeUninstall();
                break;

            default:
                throw new UnknownActionException($this->getAction());
        }

        $this->_dispatchActionEvent(self::EVENT_TIMING_POST);
    }

    /**
     * makeUpdate
     *
     * @return void
     */
    protected function makeUpdate()
    {
        $this->makeUninstall();
        $this->makeInstall();
    }

    /**
     * makeInstall
     *
     * @return void
     */
    protected function makeInstall()
    {
        $this->createDelegates();
        $this->saveMappings();

    }

    /**
     * makeUninstall
     *
     * @return void
     */
    protected function makeUninstall()
    {
        $this->removeDelegates();
    }

    /**
     * createDelegates
     *
     * @return void
     */
    protected function createDelegates()
    {
        try {
            foreach ($this->getMappingsArray() as $src => $dest) {
                $this->createDelegate(
                    $this->getFullPath($this->getSourceDir(), $src),
                    $this->getFullPath($this->getDestDir(), $dest)
                );
            }
        } catch (Mapping\Exception\MappingException $e) {
            $this->_io->write(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    /**
     * createDelegate
     *
     * @param string $src
     * @param string $dest
     *
     * @return void
     */
    protected abstract function createDelegate($src, $dest);

    /**
     * removeDelegates
     *
     * @return void
     */
    protected function removeDelegates()
    {
        foreach ($this->getDeployedDelegatesMapping() as $delegate) {
            $filePath = $this->getFullPath($this->getDestDir(), $delegate);
            $this->removeDelegate($filePath);
        }
    }

    /**
     * removeDelegate
     *
     * @param string $delegate
     *
     * @return void
     */
    protected abstract function removeDelegate($delegate);

    /**
     * getPackage
     *
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * getSourceDir
     *
     * @return SplFileInfo
     */
    protected function getSourceDir()
    {
        return $this->_sourceDir;
    }

    /**
     * getDestDir
     *
     * @return SplFileInfo
     */
    protected function getDestDir()
    {
        return $this->_destDir;
    }

    /**
     * getAction
     *
     * @return string
     */
    protected function getAction()
    {
        return $this->_action;
    }

    /**
     * getEventDispatcher
     *
     * @return EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * getIo
     *
     * @return IOInterface
     */
    protected function getIo()
    {
        return $this->_io;
    }

    /**
     * getComposer
     *
     * @return Composer
     */
    protected function getComposer()
    {
        return $this->_composer;
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->_fs;
    }

    /**
     * getMappingsArray
     *
     * @return array
     */
    protected function getMappingsArray()
    {
        return $this->getMapping()->getTranslatedMappingsArray();
    }

    /**
     * _dispatchEvent
     *
     * @param string $name name of the event
     *
     * @return void
     */
    private function _dispatchEvent($name)
    {
        $operation = new Deploy(
            $this->getPackage(),
            $this->getAction(),
            $this->getSourceDir(),
            $this->getDestDir()
        );

        $event = new PackageEvent(
            $name,
            $this->getComposer(),
            $this->getIo(),
            false,
            $operation
        );

        $this->getEventDispatcher()
            ->dispatch($name, $event);
    }

    /**
     * _dispatchPreAction
     *
     * @param string $timing pre or post
     *
     * @return void
     */
    private function _dispatchActionEvent($timing)
    {
        $name = sprintf(
            "%s-deploy-%s-%s",
            $timing,
            $this->getPackage()->getType(),
            $this->getAction()
        );

        $this->_dispatchEvent($name);
    }

    /**
     * _initMapping
     *
     * @return Mapping\AbstractMapping
     */
    private function getMapping()
    {
        if (null === $this->_mapping) {
            $this->_mapping = Mapping\Factory::get(
                $this->getPackage(),
                $this->getSourceDir()
            );
        }

        return $this->_mapping;
    }

    /**
     * getCurrentDeployedMapping
     *
     * @return array
     */
    private function getDeployedDelegatesMapping()
    {
        if (null === $this->_deployedDelegatesMapping) {
            $this->_deployedDelegatesMapping = $this->loadMappings();
        }

        return $this->_deployedDelegatesMapping;
    }

    /**
     * getFullSrc
     *
     * @param $base
     * @param $path
     *
     * @return string
     */
    protected function getFullPath($base, $path)
    {
        return $this->getFs()
            ->joinFileUris($base, $path);
    }

    /**
     * saveMappings
     *
     * @return void
     */
    protected function saveMappings()
    {
        $this->getFs()->ensureDirectoryExists(
            dirname($this->getMappingsFilePath())
        );
        file_put_contents(
            $this->getMappingsFilePath(),
            json_encode($this->getMappingsArray())
        );
    }

    /**
     * loadMappings
     *
     * @return array
     */
    protected function loadMappings()
    {
        if (file_exists($this->getMappingsFilePath())) {
            return (array)json_decode(
                file_get_contents(
                    $this->getMappingsFilePath()
                )
            );
        }

        return array();
    }

    /**
     * getMappingsFilePath
     *
     * @return string
     */
    protected function getMappingsFilePath()
    {
        $mappingsDir = $this->getFs()->joinFileUris(
            Config::getInstance()->getVendorDir(),
            self::MAPPINGS_DIR
        );

        return $this->getFs()->joinFileUris(
            $mappingsDir,
            str_replace('/', '_', $this->getPackage()->getName())
        );
    }
} 
