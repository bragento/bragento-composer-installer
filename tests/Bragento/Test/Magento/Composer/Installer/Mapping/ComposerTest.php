<?php
/**
 * ComposerTest.php
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

use Bragento\Magento\Composer\Installer\Mapping\Composer;
use Composer\Package\Package;


/**
 * Class ComposerTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class ComposerTest extends AbstractMappingTest
{
    protected function getMappingName()
    {
        return 'composer';
    }

    /**
     * provideTestData
     *
     * array($mapping, $expectedResult)
     *
     * @return array
     */
    public function provideComposerMapTestData()
    {
        $mappings = array(
            array('app/etc/modules/*', 'app/etc/modules'),
            array('app/design', 'app/design')
        );

        $results = array();
        foreach ($mappings as $mapping) {
            $result[$mapping[0]] = $mapping[1];
        }
        return array(
            array(
                $mappings,
                $results
            )
        );
    }

    /**
     * testComposerMapping
     *
     * @param $mapping
     * @param $expected
     *
     * @return void
     *
     * @dataProvider provideComposerMapTestData
     */
    public function testComposerMapping($mapping, $expected)
    {
        $package = $this->getTestPackage(
            array(
                Composer::COMPOSER_MAP_KEY => $mapping
            )
        );

        $mapping = new Composer(
            $this->getBuildDir(),
            $package
        );

        $actual = $mapping->getMappingsArray();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertEquals($value, $actual[$key]);
        }
    }

    public function testEmptyMapping()
    {
        $package = $this->getTestPackage(
            array(
                Composer::COMPOSER_MAP_KEY => array()
            )
        );

        $mapping = new Composer(
            $this->getBuildDir(),
            $package
        );

        $this->assertEquals(array(), $mapping->getMappingsArray());
    }

    public function testFaultMapping()
    {
        $package = $this->getTestPackage(
            array(
                Composer::COMPOSER_MAP_KEY => 'someString'
            )
        );

        $mapping = new Composer(
            $this->getBuildDir(),
            $package
        );

        $this->assertEquals(array(), $mapping->getMappingsArray());
    }

    public function testMappingKeyNotSet()
    {
        $mapping = new Composer(
            $this->getBuildDir(),
            $this->getTestPackage(array())
        );

        $this->assertEquals(array(), $mapping->getMappingsArray());
    }
} 
