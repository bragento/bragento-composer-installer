<?php
 /**
 * AbstractMappingTest.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   ${NAMESPACE}
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Test\Magento\Composer\Installer\Mapping;

use Composer\Package\Package;

/**
 * Class AbstractMappingTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   ${NAMESPACE}
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractMappingTest extends MappingDataProvider
{
    abstract protected function getMappingName();

    protected function getMappingsFile($name)
    {
        $file = sprintf('files/mappings/%s', $this->getMappingName());
        return $this->getFilesystem()->getFile(
            $this->getTestResDir($file),
            $name
        );
    }

    protected function copyMappingsFileToBuildDir($name)
    {
        $target = sprintf('%s/%s', $this->getBuildDir(), $this->getMappingName());
        copy($this->getMappingsFile($name), $target);
    }

    protected function getTestPackage($extra = array())
    {
        $package = new Package('test/package', '1.0.0', '1.0.0');
        $package->setExtra($extra);

        return $package;
    }
}
