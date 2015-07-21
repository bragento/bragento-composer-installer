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
 */
class Package extends AbstractMapping
{
    const PACKAGE_XML_FILE_NAME = 'package.xml';

    const TARGET_XPATH = '//contents/target';
    const NAME_ATTRIBUTE = 'name';

    const PATH_TYPE_DIR = 'dir';

    const PATH_TYPE_FILE = 'file';

    /**
     * _targets
     *
     * @var array
     */
    protected $targets
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
     * @throws Exception\InvalidTargetException
     * @throws \Exception
     * @return MapEntity[]
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
                $basePath = $this->getTargetPath($target);
                foreach ($target->children() as $child) {
                    $elPaths = $this->getElementPaths($child);
                    foreach ($elPaths as $elementPath) {
                        $relativePath
                            = $basePath
                            . DIRECTORY_SEPARATOR
                            . $elementPath;

                        $map[] = new MapEntity($relativePath, $relativePath);
                    }
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
        $attributes = $target->attributes();
        $name = (string)$attributes[self::NAME_ATTRIBUTE];
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
        return $this->targets;
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
        $typeAttributes = $element->attributes();
        $name = $typeAttributes[self::NAME_ATTRIBUTE];
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
