<?php
/**
 * Factory.php
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

use Bragento\Magento\Composer\Installer\Deploy\Manager\PackageTypes;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Factory
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Factory
{
    const STRATEGY_COPY = 'Copy';
    const STRATEGY_SYMLINK = 'Symlink';
    const STRATEGY_NONE = 'None';

    const NS = '\\Bragento\\Magento\\Composer\\Installer\\Deploy\\Strategy\\';

    /**
     * deployStrategies
     *
     * @var array
     */
    protected static $deployStrategies;

    /**
     * _composer
     *
     * @var Composer
     */
    protected static $composer;

    /**
     * Composer IO Interface
     *
     * @var IOInterface
     */
    protected static $io;

    /**
     * init
     *
     * @param Composer    $composer composer instance
     * @param IOInterface $io       IO INterface
     *
     * @return void
     */
    public static function init(Composer $composer, IOInterface $io)
    {
        self::$composer = $composer;
        self::$io = $io;
        self::$deployStrategies = array();
    }

    /**
     * getStrategy
     *
     * @param PackageInterface $package   Package to Deploy
     * @param string           $action    Deploy Action
     * @param SplFileInfo      $sourceDir Source Dir
     * @param SplFileInfo      $destDir   Target Dir
     *
     * @return AbstractStrategy
     */
    public static function get(
        PackageInterface $package,
        $action,
        SplFileInfo $sourceDir,
        SplFileInfo $destDir
    ) {
        if (!isset(self::$deployStrategies[$package->getName()])) {
            switch ($package->getType()) {
                case PackageTypes::MAGENTO_CORE:
                    $strategy = self::STRATEGY_COPY;
                    break;

                case PackageTypes::MAGENTO_MODULE:
                case PackageTypes::MAGENTO_THEME:
                    $strategy = self::STRATEGY_SYMLINK;
                    break;

                default:
                    $strategy = self::STRATEGY_NONE;
            }

            $classname = self::getClassName($strategy);

            self::$deployStrategies[$package->getName()] = new $classname(
                $package,
                $sourceDir,
                $destDir,
                $action,
                self::$composer,
                self::$io
            );
        }

        return self::$deployStrategies[$package->getName()];
    }

    protected function getPackageDeployStrategy(PackageInterface $package)
    {

    }

    /**
     * get strategy classname by strategy name
     *
     * @param string $strategy
     *
     * @return string
     */
    protected static function getClassName($strategy)
    {
        return self::NS . $strategy;
    }
}
