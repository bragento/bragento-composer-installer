<?php
/**
 * Loader.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Mapping;

use Bragento\Magento\Composer\Installer\Config\Reader;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser\Composer;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser\Modman;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser\PackageXml as PackageXml;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser\Parsable;
use Bragento\Magento\Composer\Installer\DI\FilesystemAwareInterface;
use Bragento\Magento\Composer\Installer\DI\FilesystemAwareTrait;
use Bragento\Magento\Composer\Installer\Util\Uri;
use Composer\Package\Package;
use Underscore\Types\Arrays;

/**
 * Class Loader
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Mapping
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Loader implements FilesystemAwareInterface
{
    use FilesystemAwareTrait;

    /**
     * loadMapping
     *
     * @param Package $package
     *
     * @return array
     * @throws MappingNotFoundException
     */
    public function loadMapping(Package $package)
    {
        if ($this->isComposerMapping($package)) {
            return $this->parseMapping(new Composer(), $package);
        } elseif ($this->isModmanMapping($package)) {
            return $this->parseMapping(new Modman(), $package);
        } elseif ($this->isPackageXmlMapping($package)) {
            return $this->parseMapping(new PackageXml(), $package);
        } else {
            throw new MappingNotFoundException($package->getName());
        }
    }

    /**
     * parseMapping
     *
     * @param Parsable $parser
     *
     * @return array
     */
    protected function parseMapping(Parsable $parser, Package $package)
    {
        return $parser->parse($package);
    }

    /**
     * isModmanMapping
     *
     * @param Package $package
     *
     * @return bool
     */
    protected function isModmanMapping(Package $package)
    {
        return $this->checkPackageFileExists(
            $package,
            Modman::MODMAN_FILENAME
        );
    }

    /**
     * isComposerMapping
     *
     * @param Package $package
     *
     * @return bool
     */
    protected function isComposerMapping(Package $package)
    {
        return (bool)Arrays::size(
            (new Reader())
                ->read($package->getTargetDir())
                ->getMap()
        );
    }

    /**
     * isPackageXmlMapping
     *
     * @param Package $package
     *
     * @return bool
     */
    protected function isPackageXmlMapping(Package $package)
    {
        return $this->checkPackageFileExists(
            $package,
            PackageXml::PACKAGEXML_FILENAME
        );
    }

    /**
     * checkFileExists
     *
     * @param Package $package
     * @param         $file
     *
     * @return bool
     */
    protected function checkPackageFileExists(Package $package, $file)
    {
        return $this->getFilesystem()->exists(
            Uri::join([
                $package->getTargetDir(),
                $file
            ], DIRECTORY_SEPARATOR)
        );
    }
}
