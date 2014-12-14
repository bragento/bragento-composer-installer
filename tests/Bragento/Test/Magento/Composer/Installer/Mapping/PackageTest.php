<?php
/**
 * PackageTest.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Test\Magento\Composer\Installer\Mapping;

use Bragento\Magento\Composer\Installer\Mapping\Package;

/**
 * Class PackageTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class PackageTest extends AbstractMappingTest
{
    protected function getMappingName()
    {
        return 'package.xml';
    }


    /**
     * provideCorrectPackageFileNames
     *
     * * array(
     * *    array($filename, $mappingscount)
     * * )
     *
     * @return array
     */
    public function provideCorrectPackageFileNames()
    {
        return array(
            array('correct.xml', 21)
        );
    }

    /**
     * testCorrectMappings
     *
     * @param $filename
     * @param $mappingscount
     *
     * @return void
     *
     * @dataProvider provideCorrectPackageFileNames
     */
    public function testCorrectMappings($filename, $mappingscount)
    {
        $this->copyMappingsFileToBuildDir($filename);
        $mapping = new Package(
            $this->getBuildDir(),
            $this->getTestPackage()
        );

        $this->assertEquals(
            $mappingscount,
            count($mapping->getMappingsArray())
        );
    }

    /**
     * testUnknownPathtype
     *
     * @return void
     *
     * @expectedException \Bragento\Magento\Composer\Installer\Mapping\Exception\UnknownPathtypeException
     */
    public function testUnknownPathtype()
    {
        $this->copyMappingsFileToBuildDir('unknownpathtype.xml');
        $mapping = new Package(
            $this->getBuildDir(),
            $this->getTestPackage()
        );

        $mapping->getMappingsArray();
    }

    /**
     * testUnknownPathtype
     *
     * @return void
     *
     * @expectedException \Bragento\Magento\Composer\Installer\Mapping\Exception\InvalidTargetException
     */
    public function testInvalidTarget()
    {
        $this->copyMappingsFileToBuildDir('invalidtarget.xml');
        $mapping = new Package(
            $this->getBuildDir(),
            $this->getTestPackage()
        );

        $mapping->getMappingsArray();
    }
}
