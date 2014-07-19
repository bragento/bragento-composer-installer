<?php
/**
 * AbstractInstaller.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Installer;

use Bragento\Magento\Composer\Installer\Deploy;
use Bragento\Magento\Composer\Installer\Project\Config;
use Composer\Composer;
use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class AbstractInstaller
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractInstaller
    extends LibraryInstaller
    implements InstallerInterface
{
    /**
     * Deploy Manager Instance
     *
     * @var Deploy\Manager
     */
    private $_dm;

    /**
     * Constructor
     *
     * @param IOInterface    $io         IO Interface
     * @param Composer       $composer   Composer Instance
     * @param Deploy\Manager $dm         Deploy Manager
     * @param string         $type
     * @param Filesystem     $filesystem Filesystem Util
     */
    public function __construct(
        IOInterface $io,
        Composer $composer,
        Deploy\Manager $dm,
        $type = 'library',
        Filesystem $filesystem = null
    ) {
        parent::__construct($io, $composer, $type, $filesystem);
        $this->_dm = $dm;
    }

    /**
     * install
     *
     * @param InstalledRepositoryInterface $repo    Installed Repository
     * @param PackageInterface             $package Installed Package
     *
     * @return void
     */
    public function install(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::install($repo, $package);
        $this->getDeployManager()->addEntry(
            $this->getDeployManagerEntry(
                $package,
                Deploy\Manager\Actions::INSTALL
            )
        );
    }

    /**
     * update
     *
     * @param InstalledRepositoryInterface $repo    Installed Repository
     * @param PackageInterface             $initial Pre Update Package
     * @param PackageInterface             $target  Post Update Package
     *
     * @return void
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        parent::update($repo, $initial, $target);
        $this->getDeployManager()->addEntry(
            $this->getDeployManagerEntry(
                $target,
                Deploy\Manager\Actions::UPDATE
            )
        );
    }

    /**
     * uninstall
     *
     * @param InstalledRepositoryInterface $repo    Installed Repo
     * @param PackageInterface             $package Package to uninstall
     *
     * @return void
     */
    public function uninstall(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::uninstall($repo, $package);
        $this->getDeployManager()->addEntry(
            $this->getDeployManagerEntry(
                $package,
                Deploy\Manager\Actions::UNINSTALL
            )
        );
    }

    /**
     * getDeployManager
     *
     * @return Deploy\Manager
     */
    protected function getDeployManager()
    {
        return $this->_dm;
    }

    /**
     * getDeployStrategy
     *
     * @param PackageInterface $package
     * @param string           $action
     *
     * @return Deploy\Strategy\AbstractStrategy
     */
    protected function getDeployStrategy(PackageInterface $package, $action)
    {
        return Deploy\Strategy\Factory::get(
            $package,
            $action,
            $this->getSourceDir($package),
            $this->getTargetDir()
        );
    }

    /**
     * getDeployManagerEntry
     *
     * @param PackageInterface $package package to deploy
     * @param string           $action
     *
     * @return Deploy\Manager\Entry
     */
    protected function getDeployManagerEntry(PackageInterface $package, $action)
    {
        return new Deploy\Manager\Entry(
            $this->getDeployStrategy($package, $action)
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
     * getTargetDir
     *
     * @return SplFileInfo
     */
    protected function getTargetDir()
    {
        return Config::getInstance()->getMagentoRootDir();
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->filesystem;
    }
} 
