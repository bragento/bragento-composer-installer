<?php
/**
 * MappingTest.php
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
use Bragento\Magento\Composer\Installer\Mapping\Factory;
use Bragento\Magento\Composer\Installer\Mapping\Modman;


/**
 * Class MappingTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer\Mapping
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class MappingTest extends AbstractMappingTest
{
    protected function getMappingName()
    {
        return 'global';
    }

    /**
     * testResolveMappings
     *
     * @return void
     */
    public function testResolveMappings()
    {
        $this->copyFiles('modman');

        $modman = new Modman(
            $this->getBuildDir(),
            $this->getTestPackage()
        );

        $this->assertEquals(2, count($modman->getResolvedMappingsArray()));
    }

    public function testFactoryGetModmanMap()
    {
        $this->copyFiles('modman');

        $mapping = Factory::get(
            $this->getTestPackage(),
            $this->getBuildDir()
        );

        $this->assertInstanceOf(
            '\\Bragento\\Magento\\Composer\\Installer\\Mapping\\Modman',
            $mapping
        );
    }

    public function testFactoryGetPackageMap()
    {
        $this->copyFiles('package');

        $mapping = Factory::get(
            $this->getTestPackage(),
            $this->getBuildDir()
        );

        $this->assertInstanceOf(
            '\\Bragento\\Magento\\Composer\\Installer\\Mapping\\Package',
            $mapping
        );
    }

    public function testFactoryGetComposerMap()
    {
        $mapping = Factory::get(
            $this->getTestPackage(
                array(
                    Composer::COMPOSER_MAP_KEY => array(
                        array('test', 'test')
                    )
                )
            ),
            $this->getBuildDir()
        );

        $this->assertInstanceOf(
            '\\Bragento\\Magento\\Composer\\Installer\\Mapping\\Composer',
            $mapping
        );
    }

    /**
     * testMappingNotFound
     *
     * @return void
     *
     * @expectedException \Bragento\Magento\Composer\Installer\Mapping\Exception\MappingNotFoundException
     */
    public function testMappingNotFound()
    {
        Factory::get(
            $this->getTestPackage(),
            $this->getBuildDir()
        );
    }

    protected function copyFiles($name)
    {
        $dir = sprintf('files/mappings/global/%s', $name);
        $this->getFs()->rcopy(
            $this->getTestDir($dir),
            $this->getBuildDir()
        );
    }
} 
