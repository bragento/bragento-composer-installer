<?php
/**
 * Factory.php
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

use Bragento\Magento\Composer\Installer\Deploy\Manager;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;


/**
 * Class Factory
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Factory
{
    const TYPE_PREFIX = 'magento-';

    const NS = '\\Bragento\\Magento\\Composer\\Installer\\Installer\\';

    /**
     * _installers
     *
     * @var AbstractInstaller[]
     */
    protected static $_installers;

    /**
     * _composer
     *
     * @var Composer
     */
    protected static $_composer;

    /**
     * _io
     *
     * @var IOInterface
     */
    protected static $_io;

    /**
     * _dm
     *
     * @var Manager
     */
    protected static $_dm;

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected static $_fs;

    /**
     * init Installer Factory
     *
     * @param Composer    $composer
     * @param IOInterface $io
     * @param Manager     $dm
     * @param Filesystem  $fs
     *
     * @return void
     */
    public static function init(
        Composer $composer,
        IOInterface $io,
        Manager $dm,
        Filesystem $fs
    ) {
        self::$_composer = $composer;
        self::$_io = $io;
        self::$_dm = $dm;
        self::$_fs = $fs;
    }

    /**
     * get Installer Instance
     *
     * @param $type
     *
     * @return AbstractInstaller
     */
    public static function get($type)
    {
        if (!isset(self::$_installers[$type])) {
            $className = self::getInstallerClass($type);
            /** @todo check class exists and instance of AbstractInstaller */
            self::$_installers[$type] = new $className(
                self::$_io,
                self::$_composer,
                self::$_dm,
                self::$_fs
            );
        }

        return self::$_installers[$type];
    }

    /**
     * get Installer Classname from Type
     *
     * @param $type
     *
     * @return string
     */
    protected static function getInstallerClass($type)
    {
        return
            self::NS .
            ucfirst(strtolower(str_replace(self::TYPE_PREFIX, '', $type)));
    }
} 
