<?php
/**
 * Factory.php
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

use Bragento\Magento\Composer\Installer\Installer\Types;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class Factory
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
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
    protected static $_deployStrategies;

    /**
     * _composer
     *
     * @var Composer
     */
    protected static $_composer;

    protected static $_io;

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
        self::$_composer = $composer;
        self::$_io = $io;
        self::$_deployStrategies = array();
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
        if (!isset(self::$_deployStrategies[$package->getName()])) {
            switch ($package->getType()) {
                case Types::MAGENTO_CORE:
                    $strategy = self::STRATEGY_COPY;
                    break;

                case Types::MAGENTO_MODULE:
                case Types::MAGENTO_THEME:
                    $strategy = self::STRATEGY_SYMLINK;
                    break;

                default:
                    $strategy = self::STRATEGY_NONE;
            }

            $classname = self::getClassName($strategy);

            self::$_deployStrategies[$package->getName()] = new $classname(
                $package,
                $sourceDir,
                $destDir,
                $action,
                self::$_composer,
                self::$_io
            );
        }

        return self::$_deployStrategies[$package->getName()];
    }

    protected static function getClassName($strategy)
    {
        return self::NS . $strategy;
    }
} 
