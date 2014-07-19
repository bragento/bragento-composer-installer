<?php
/**
 * Config.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Project
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Project;

use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Composer;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class Config
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Project
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 *
 * @todo      load values from composer.json
 * @todo      add all config values
 */
class Config
{
    /**
     * _instance
     *
     * @var Config
     */
    protected static $_instance;

    /**
     * _composer
     *
     * @var Composer
     */
    protected static $_composer;

    /**
     * _magentoRootDir
     *
     * @var SplFileInfo
     */
    protected $_magentoRootDir;

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected $_fs;

    /**
     * private constructor for singleton
     */
    private function __construct()
    {
        $this->_fs = new Filesystem();
    }

    /**
     * getMagentoRootDir
     *
     * @return SplFileInfo
     */
    public function getMagentoRootDir()
    {
        /** @todo load magento root dir from config */
        if (null === $this->_magentoRootDir) {
            $this->_fs->ensureDirectoryExists('magento');
            $this->_magentoRootDir = new SplFileInfo(
                'magento',
                'magento',
                'magento'
            );
        }
        return $this->_magentoRootDir;
    }

    public function getVendorDir()
    {
        return self::$_composer->getConfig()->get('vendor-dir');
    }

    public static function init(Composer $composer)
    {
        self::$_composer = $composer;
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new Config();
        }

        return self::$_instance;
    }
} 
