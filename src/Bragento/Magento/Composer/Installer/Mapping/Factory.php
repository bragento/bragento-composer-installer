<?php
/**
 * Factory.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Mapping;

use Bragento\Magento\Composer\Installer\Mapping\Exception\MappingNotFoundException;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Package\PackageInterface;


/**
 * Class Factory
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Factory
{
    /**
     * get
     *
     * @param PackageInterface $package
     * @param string           $moduleDir
     *
     * @throws Exception\MappingNotFoundException
     * @return AbstractMapping
     */
    public static function get(
        PackageInterface $package,
        $moduleDir
    ) {
        $fs = new Filesystem();
        /** @todo implement package.xml and composer mappings */
        if (self::isModman($moduleDir, $fs)) {
            return new Modman($moduleDir);
        } elseif (self::isPackage($moduleDir, $fs)) {
            return new Package($moduleDir);
        } else {
            throw new MappingNotFoundException($package);
        }
    }

    /**
     * isModman
     *
     * @param string     $moduleDir
     * @param Filesystem $fs
     *
     * @return bool
     */
    protected static function isModman(
        $moduleDir,
        Filesystem $fs
    ) {
        return file_exists(
            $fs->joinFileUris(
                $moduleDir,
                Modman::MODMAN_FILE_NAME
            )
        );
    }

    /**
     * isPackage
     *
     * @param            $moduleDir
     * @param Filesystem $fs
     *
     * @return bool
     */
    protected static function isPackage(
        $moduleDir,
        Filesystem $fs
    ) {
        return file_exists(
            $fs->joinFileUris(
                $moduleDir,
                Package::PACKAGE_XML_FILE_NAME
            )
        );
    }
} 
