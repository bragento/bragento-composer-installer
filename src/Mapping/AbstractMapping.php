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
     * @var MapEntity[]
     */
    protected $mappingsArray;

    /**
     * _moduleDir
     *
     * @var SplFileInfo
     */
    private $moduleDir;

    /**
     * _fs
     *
     * @var Filesystem
     */
    private $fs;

    /**
     * _package
     *
     * @var PackageInterface
     */
    private $package;

    /**
     * construct mappings
     *
     * @param SplFileInfo      $moduleDir
     * @param PackageInterface $package
     */
    public function __construct(
        SplFileInfo $moduleDir,
        PackageInterface $package
    ) {
        $this->fs = new Filesystem();
        $this->moduleDir = $moduleDir;
        $this->package = $package;
    }

    /**
     * getTranslatedMappingsArray
     *
     * parse mappings like wildcards
     *
     * @return MapEntity[]
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
     * @return MapEntity[]
     */
    public function resolveMappings(array $mappings)
    {
        $translatedMap = array();
        /** @var MapEntity $map */
        foreach ($mappings as $map) {
            $src = $map->getSource();
            $dest = $map->getTarget();
            if (String::contains($src, '*')) {
                foreach (glob($this->getFs()->joinFileUris($this->getModuleDir(), $src)) as $file) {
                    $fileSrc = $this->getFs()->rmAbsPathPart(
                        $file,
                        $this->getModuleDir()
                    );
                    $translatedMap[] = new MapEntity(
                        $this->getFs()->trimDs($fileSrc),
                        $this->getFs()->trimDs(
                            $this->getFs()->joinFileUris(
                                $dest,
                                basename($file),
                                false
                            )
                        )
                    );
                }
            } else {
                if ($this->getFs()->endsWithDs($dest)) {
                    if ($this->getFs()->endsWithDs($src)) {
                        $src = $this->getFs()->removeTrailingDs($src);
                    } else {
                        if (is_file($src)) {
                            $dest = $this->getFs()->joinFileUris(
                                $dest,
                                basename($src),
                                false
                            );
                        } else {
                            $dest = $this->getFs()->removeTrailingDs($dest);
                        }
                    }
                }
                $translatedMap[] = new MapEntity(
                    $this->getFs()->trimDs($src),
                    $this->getFs()->trimDs($dest)
                );
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
        return $this->moduleDir;
    }

    /**
     * getMappingsArray
     *
     * @return MapEntity[]
     */
    public function getMappingsArray()
    {
        if (null === $this->mappingsArray) {
            $this->mappingsArray = $this->parseMappings();
        }

        return $this->mappingsArray;
    }

    /**
     * _pathMappingTranslations
     *
     * get the mappings from the source and return them
     *
     * @return MapEntity[]
     */
    abstract protected function parseMappings();

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
     * getPackage
     *
     * @return PackageInterface
     */
    protected function getPackage()
    {
        return $this->package;
    }
}
