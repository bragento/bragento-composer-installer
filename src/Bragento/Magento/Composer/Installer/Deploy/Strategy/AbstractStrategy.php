<?php
/**
 * AbstractStrategy.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;

use Bragento\Magento\Composer\Installer\Deploy\Exception\UnknownActionException;
use Bragento\Magento\Composer\Installer\Deploy\Manager\Actions;
use Bragento\Magento\Composer\Installer\Deploy\Operation\DeployPackage;
use Bragento\Magento\Composer\Installer\Deploy\State;
use Bragento\Magento\Composer\Installer\Mapping;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Script\PackageEvent;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class AbstractStrategy
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractStrategy
{
    const EVENT_TIMING_PRE = 'pre';
    const EVENT_TIMING_POST = 'post';

    const STATE_DIR = '.mage-deploy';

    /**
     * _package
     *
     * @var PackageInterface
     */
    private $package;

    /**
     * _sourceDir
     *
     * @var SplFileInfo
     */
    private $sourceDir;

    /**
     * _destDir
     *
     * @var SplFileInfo
     */
    private $destDir;

    /**
     * _action
     *
     * @var string
     */
    private $action;

    /**
     * _eventDispatcher
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * _composer
     *
     * @var Composer
     */
    private $composer;

    /**
     * _io
     *
     * @var IOInterface
     */
    private $io;

    /**
     * _mapping
     *
     * @var Mapping\AbstractMapping
     */
    private $mapping;

    /**
     * _deployedFilesMapping
     *
     * @var array
     */
    private $deployedDelegatesMapping;

    /**
     * _fs
     *
     * @var Filesystem
     */
    private $fs;

    /**
     * _state
     *
     * @var State
     */
    private $state;

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
    public function __construct(
        PackageInterface $package,
        SplFileInfo $sourceDir,
        SplFileInfo $destDir,
        $action,
        Composer $composer,
        IOInterface $io
    ) {
        $this->package = $package;
        $this->sourceDir = $sourceDir;
        $this->destDir = $destDir;
        $this->action = $action;
        $this->eventDispatcher = $composer->getEventDispatcher();
        $this->composer = $composer;
        $this->io = $io;
        $this->fs = new Filesystem();
        $this->state = new State($this);
    }

    /**
     * doDeploy
     *
     * @throws UnknownActionException
     * @return void
     */
    public function doDeploy()
    {
        $this->dispatchActionEvent(self::EVENT_TIMING_PRE);

        switch ($this->getAction()) {
            case Actions::INSTALL:
                $this->makeInstall();
                break;

            case Actions::UPDATE:
                $this->makeUpdate();
                break;

            case Actions::UNINSTALL:
                $this->makeUninstall(true);
                break;

            default:
                throw new UnknownActionException($this->getAction());
        }

        $this->dispatchActionEvent(self::EVENT_TIMING_POST);
    }

    /**
     * _dispatchPreAction
     *
     * @param string $timing pre or post
     *
     * @return void
     */
    private function dispatchActionEvent($timing)
    {
        $name = sprintf(
            "%s-deploy-%s-%s",
            $timing,
            $this->getPackage()->getType(),
            $this->getAction()
        );

        $this->dispatchEvent($name);
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        $reflect = new ReflectionClass($this);
        return strtolower($reflect->getShortName());
    }

    /**
     * getPackage
     *
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * getAction
     *
     * @return string
     */
    protected function getAction()
    {
        return $this->action;
    }

    /**
     * _dispatchEvent
     *
     * @param string $name name of the event
     *
     * @return void
     */
    private function dispatchEvent($name)
    {
        $operation = new DeployPackage(
            $this->getPackage(),
            $this->getAction(),
            $this->getSourceDir(),
            $this->getDestDir(),
            $this->getName()
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
     * getSourceDir
     *
     * @return SplFileInfo
     */
    protected function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * getDestDir
     *
     * @return SplFileInfo
     */
    protected function getDestDir()
    {
        return $this->destDir;
    }

    /**
     * getComposer
     *
     * @return Composer
     */
    protected function getComposer()
    {
        return $this->composer;
    }

    /**
     * getIo
     *
     * @return IOInterface
     */
    protected function getIo()
    {
        return $this->io;
    }

    /**
     * getEventDispatcher
     *
     * @return EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * makeInstall
     *
     * @return void
     */
    protected function makeInstall()
    {
        $this->createDelegates();
        $this->getState()->setMapping($this->getMappingsArray());
        $this->getState()->save();
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
                $src = $this->getFullPath($this->getSourceDir(), $src);
                $dest = $this->getFullPath($this->getDestDir(), $dest);
                if ($this->getFs()->exists($dest)) {
                    if (!$override = Config::getInstance()->isForcedOverride()) {
                        $override = $this->getIo()
                            ->ask(
                                sprintf(
                                    "Destination already exists. Replace %s ? [y/n] ",
                                    $dest
                                )
                            );
                    }
                    if ($override) {
                        $this->getFs()->remove($dest);
                    } else {
                        return;
                    }
                }
                $this->getFs()->ensureDirectoryExists(dirname($dest));
                $this->createDelegate($src, $dest);
            }
        } catch (\Exception $e) {
            $this->io->write(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    /**
     * getMappingsArray
     *
     * @return array
     */
    protected function getMappingsArray()
    {
        return $this->getMapping()->getResolvedMappingsArray();
    }

    /**
     * _initMapping
     *
     * @return Mapping\AbstractMapping
     */
    private function getMapping()
    {
        if (null === $this->mapping) {
            $this->mapping = Mapping\Factory::get(
                $this->getPackage(),
                $this->getSourceDir()
            );
        }

        return $this->mapping;
    }

    /**
     * createDelegate
     *
     * @param string $src
     * @param string $dest
     *
     * @return void
     */
    abstract protected function createDelegate($src, $dest);

    /**
     * getFullSrc
     *
     * @param SplFileInfo $base
     * @param string      $path
     *
     * @return string
     */
    protected function getFullPath($base, $path)
    {
        return $this->getFs()
            ->joinFileUris($base->getPathname(), $path);
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->fs;
    }

    /**
     * getState
     *
     * @return State
     */
    protected function getState()
    {
        return $this->state;
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
     * makeUninstall
     *
     * @param bool $deleteState
     *
     * @return void
     */
    protected function makeUninstall($deleteState = false)
    {
        $this->removeDelegates();
        if ($deleteState) {
            $this->getState()->delete();
        }
    }

    /**
     * removeDelegates
     *
     * @return void
     */
    protected function removeDelegates()
    {
        if (is_array($this->getDeployedDelegatesMapping())) {
            foreach ($this->getDeployedDelegatesMapping() as $source => $destination) {
                $filePath = $this->getFullPath(Config::getInstance()->getMagentoRootDir(), $destination);
                if ($this->getFs()->exists($filePath)) {
                    $this->removeDelegate($filePath);
                }
            }
        }
    }

    /**
     * getCurrentDeployedMapping
     *
     * @return array
     */
    private function getDeployedDelegatesMapping()
    {
        if (null === $this->deployedDelegatesMapping) {
            $this->deployedDelegatesMapping = $this->getState()->getMapping();
        }

        return $this->deployedDelegatesMapping;
    }

    /**
     * removeDelegate
     *
     * @param string $delegate
     *
     * @return mixed
     */
    protected function removeDelegate($delegate)
    {
        $this->getFs()->remove($delegate);
    }
}
