<?php
/**
 * AbstractMapping.php
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

use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Bragento\Magento\Composer\Installer\Util\String;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class AbstractMapping
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractMapping
{
    /**
     * _mappings
     *
     * @var array
     */
    protected $_mappingsArray;

    /**
     * _moduleDir
     *
     * @var SplFileInfo
     */
    private $_moduleDir;

    /**
     * _fs
     *
     * @var Filesystem
     */
    private $_fs;

    /**
     * _package
     *
     * @var PackageInterface
     */
    private $_package;

    /**
     * construct mappings
     *
     * @param SplFileInfo      $moduleDir
     * @param PackageInterface $package
     */
    function __construct(
        SplFileInfo $moduleDir,
        PackageInterface $package
    ) {
        $this->_fs = new Filesystem();
        $this->_moduleDir = $moduleDir;
        $this->_package = $package;
    }

    /**
     * getTranslatedMappingsArray
     *
     * parse mappings like wildcards
     *
     * @return array
     */
    public function getResolvedMappingsArray()
    {
        return $this->resolveMappings($this->getMappingsArray());
    }

    /**
     * translateMappings
     *
     * @param array $mappings
     *
     * @return array
     */
    protected function resolveMappings(array $mappings)
    {
        $translatedMap = array();
        foreach ($mappings as $src => $dest) {
            if (String::contains($src, '*')) {
                $glob = $this->getModuleDir() . DIRECTORY_SEPARATOR . $src;
                foreach (glob($glob) as $file) {
                    $newSrcParts = explode('/', $file);
                    foreach (explode('/', $this->getModuleDir()) as $part) {
                        array_shift($newSrcParts);
                    }
                    $newSrc = implode('/', $newSrcParts);
                    $newDest = ltrim($dest . basename($file), '/\\');
                    $translatedMap[$newSrc] = $newDest;
                }
            } elseif (String::endsWith($dest, '/') && is_file($src)) {
                $translatedMap[$src] = sprintf(
                    '%s/%s',
                    $dest,
                    basename($src)
                );
            } else {
                $translatedMap[$src] = $dest;
            }
        }

        return $translatedMap;
    }

    /**
     * getModuleDir
     *
     * @return SplFileInfo
     */
    protected function getModuleDir()
    {
        return $this->_moduleDir;
    }

    /**
     * getMappingsArray
     *
     * @return array
     */
    public function getMappingsArray()
    {
        if (null === $this->_mappingsArray) {
            $this->_mappingsArray = $this->parseMappings();
        }

        return $this->_mappingsArray;
    }

    /**
     * _pathMappingTranslations
     *
     * get the mappings from the source and return them
     *
     * * $example = array(
     * *    $source1 => $target1,
     * *    $source2 => target2
     * * )
     *
     * @return array
     */
    abstract protected function parseMappings();

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
     * getPackage
     *
     * @return PackageInterface
     */
    protected function getPackage()
    {
        return $this->_package;
    }
} 
