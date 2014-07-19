<?php
/**
 * Packages.php
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

use Bragento\Magento\Composer\Installer\Mapping\Exception\InvalidTargetException;
use Bragento\Magento\Composer\Installer\Mapping\Exception\UnknownPathtypeException;
use SimpleXMLElement;


/**
 * Class Packages
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 *
 * @todo      implement parsing for package.xml mappings
 */
class Package extends AbstractMapping
{
    const PACKAGE_XML_FILE_NAME = 'package.xml';

    const TARGET_XPATH = '//contents/target';

    const PATH_TYPE_DIR = 'dir';

    const PATH_TYPE_FILE = 'file';

    /**
     * _targets
     *
     * @var array
     */
    protected $_targets
        = array(
            'magelocal'     => './app/code/local',
            'magecommunity' => './app/code/community',
            'magecore'      => './app/code/core',
            'magedesign'    => './app/design',
            'mageetc'       => './app/etc',
            'magelib'       => './lib',
            'magelocale'    => './app/locale',
            'magemedia'     => './media',
            'mageskin'      => './skin',
            'mageweb'       => '.',
            'magetest'      => './tests',
            'mage'          => '.'
        );

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
     * @throws Exception\InvalidTargetException
     * @throws \Exception
     * @return array
     */
    protected function parseMappings()
    {
        $map = array();

        /** @var $package SimpleXMLElement */
        $package = simplexml_load_file(
            $this->getPackageXmlFileObject()->getPathname()
        );

        if (isset($package)) {
            foreach ($package->xpath(self::TARGET_XPATH) as $target) {
                try {
                    $basePath = $this->getTargetPath($target);

                    foreach ($target->children() as $child) {
                        $elPaths = $this->getElementPaths($child);
                        foreach ($elPaths as $elementPath) {
                            $relativePath
                                = $basePath
                                . DIRECTORY_SEPARATOR
                                . $elementPath;

                            $map[$relativePath] = $relativePath;
                        }
                    }
                } catch (InvalidTargetException $e) {
                    // Skip invalid targets
                    throw $e;
                    continue;
                }
            }
        }
        return $map;
    }

    /**
     * getTargetPath
     *
     * @param SimpleXMLElement $target
     *
     * @return mixed
     * @throws Exception\InvalidTargetException
     */
    protected function getTargetPath(\SimpleXMLElement $target)
    {
        $name = (string)$target->attributes()->name;
        $targets = $this->getTargetsDefinitions();
        if (!isset($targets[$name])) {
            throw new InvalidTargetException($name);
        }
        return $targets[$name];
    }

    /**
     * getTargetsDefinitions
     *
     * @return array
     */
    protected function getTargetsDefinitions()
    {
        return $this->_targets;
    }

    /**
     * getPackageXmlFileObject
     *
     * @return \SplFileObject
     */
    protected function getPackageXmlFileObject()
    {
        return new \SplFileObject(
            $this->getFs()->getFile(
                $this->getModuleDir()->getPathname(),
                self::PACKAGE_XML_FILE_NAME
            )->getPathname()
        );
    }

    /**
     * getElementPaths
     *
     * @param SimpleXMLElement $element
     *
     * @return array
     * @throws Exception\UnknownPathtypeException
     */
    protected function getElementPaths(\SimpleXMLElement $element)
    {
        $type = $element->getName();
        $name = $element->attributes()->name;
        $elementPaths = array();

        switch ($type) {
            case self::PATH_TYPE_DIR:
                if ($element->children()) {
                    foreach ($element->children() as $child) {
                        $elPaths = $this->getElementPaths($child);
                        foreach ($elPaths as $elementPath) {
                            $elementPaths[]
                                = $name == '.'
                                ? $elementPath
                                : $name . '/' . $elementPath;
                        }
                    }
                } else {
                    $elementPaths[] = $name;
                }
                break;

            case self::PATH_TYPE_FILE:
                $elementPaths[] = $name;
                break;

            default:
                throw new UnknownPathtypeException($type);
        }

        return $elementPaths;
    }
} 
